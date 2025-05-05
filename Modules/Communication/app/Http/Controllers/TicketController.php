<?php

namespace Modules\Communication\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Communication\Models\TicketCategory;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\Communication\Models\Ticket;
use Modules\Communication\Models\TicketHistory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class TicketController extends Controller
{
    public function index(): View
    {
        $category = TicketCategory::all();
        $users = User::whereIn('user_type', [1, 2])->get();
        return view('communication::ticket.index', compact('category', 'users'));
    }
    public function ticketDetails(): View
    {
        $category = TicketCategory::all();
        return view('communication::ticket.admin-ticket-details', compact('category'));
    }
    public function userTicket(): View
    {
        $category = TicketCategory::all();
        $seo_title = __('web.user.tickets');
        return view('communication::ticket.user-ticket', compact('category', 'seo_title'));
    }

    public function userTicketStore(Request $request): JsonResponse
    {
        try {
            // Get user from either 'admin' or 'web' guard
            $user = Auth::guard('admin')->check() ? Auth::guard('admin')->user() : Auth::guard('web')->user();

            if (!$user) {
                return response()->json([
                    'code' => 401,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'category' => 'required|integer|exists:ticket_categories,id',
                'priority' => 'required|string|in:Low,Medium,High',
                'description' => 'required|string|max:1000',
                'document.*' => 'nullable|file|mimes:pdf,txt,doc,docx|max:51200'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => 422,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Generate ticket ID
            $latestTicket = Ticket::latest('id')->first();
            $nextId = $latestTicket ? $latestTicket->id + 1 : 1;
            $ticketId = 'TICKET-' . str_pad((string) $nextId, 6, '0', STR_PAD_LEFT);

            // Handle file uploads
            $filePaths = [];
            $file = $request->file('document');
            if ($file instanceof \Illuminate\Http\UploadedFile) {
                $filePath = $file->store('tickets', 'public');
                $filePaths[] = $filePath;
            }

            // Create ticket
            $ticket = Ticket::create([
                'ticket_id' => $ticketId,
                'priority' => $request->priority,
                'user_id' => $user->id,
                'description' => $request->description,
                'status' => 1,
                'subject' => $request->category,
                'user_type' => $user->user_type ?? 3,
                'attachment' => count($filePaths) > 0 ? json_encode($filePaths) : null,
                'created_by' => $user->id,
            ]);

            return response()->json([
                'code' => 200,
                'message' => 'Ticket created successfully',
                'data' => $ticket
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'code' => 500,
                'message' => 'An error occurred while creating the ticket',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function listTickets(Request $request): JsonResponse
    {
        try {
            $user = current_user();

            if (!$user instanceof \App\Models\User) {
                return response()->json([
                    'code' => 401,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            $ticketId = $request->input('ticketId');
            $priorityFilters = $request->input('priority', []);
            $statusFilters = $request->input('status', []);
            $sortBy = $request->input('sort_by', 'latest');
            $searchTerm = $request->input('search', '');

            $withRelations = [
                'user:id,name,email',
                'user.userDetail:id,user_id,first_name,last_name,profile_image',
                'category:id,name',
                'assignee:id,name,email',
                'assignee.userDetail:id,user_id,first_name,last_name,profile_image',
                'ticketHistories:id,ticket_id,user_id,description,created_by,updated_by,created_at',
                'ticketHistories.user:id,name,email',
                'ticketHistories.user.userDetail:id,user_id,first_name,last_name,profile_image',
            ];


            $query = Ticket::query()->with($withRelations);

            // Apply user-specific filters
            if ($user->user_type == 1) {
                // Admin can see all tickets
                if ($ticketId) {
                    $query->where('id', $ticketId);
                }
            } elseif ($user->user_type == 3) {
                // Regular user can only see their own tickets
                $query->where('user_id', $user->id);
                if ($ticketId) {
                    $query->where('id', $ticketId);
                }
            } elseif ($user->user_type == 2) {
                // Assignee can only see tickets assigned to them
                $query->where('assignee_id', $user->id);
                if ($ticketId) {
                    $query->where('id', $ticketId);
                }
            } else {
                return response()->json([
                    'code' => 403,
                    'message' => 'Unauthorized access',
                    'user' => $user
                ], 403);
            }

            // Apply priority filters
            if (!empty($priorityFilters)) {
                $query->whereIn('priority', $priorityFilters);
            }

            // Apply status filters
            if (!empty($statusFilters)) {
                $query->whereIn('status', $statusFilters);
            }

            // Apply search filter
            if (!empty($searchTerm)) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('ticket_id', 'like', "%{$searchTerm}%")
                      ->orWhereHas('user', function ($q2) use ($searchTerm) {
                          $q2->where('name', 'like', "%{$searchTerm}%");
                      })
                      ->orWhereHas('category', function ($q2) use ($searchTerm) {
                          $q2->where('name', 'like', "%{$searchTerm}%");
                      });
                });
            }

            // Apply sorting
            switch ($sortBy) {
                case 'ascending':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'descending':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'last month':
                    $query->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()]);
                    break;
                case 'last 7 days':
                    $query->whereBetween('created_at', [now()->subDays(7), now()]);
                    break;
                default:
                    $query->latest();
                    break;
            }

            $tickets = $query->get();

            return response()->json([
                'code' => 200,
                'message' => __('admin.common.default_retrieve_success'),
                'data' => $tickets
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function ticketUpdateAsssign(Request $request): JsonResponse
    {
        try {
            $user = Auth::guard('admin')->check() ? Auth::guard('admin')->user() : Auth::guard('web')->user();

            if (!$user) {
                return response()->json([
                    'code' => 401,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'ticketid' => 'required|exists:tickets,id',
                'assign_staff' => 'required|exists:users,id',
                'reply' => 'nullable|string|max:3000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => 422,
                    'errors' => $validator->errors()
                ], 422);
            }
          /** @var \Modules\Communication\Models\Ticket $ticket */
            $ticket = Ticket::findOrFail($request->ticketid);

            // Only assign if the ticket is still open and not yet assigned
            if ($user->user_type == 1) {
                if ($ticket->status != 1) {
                    return response()->json([
                        'code' => 400,
                        'message' => __('admin.support.ticket_assignment_failed_due_to_status')
                    ], 400);
                }

                $ticket->assignee_id = $request->assign_staff;
                $ticket->status = 2;
            }

            $ticket->updated_by = $user->id;
            $ticket->save();

            if ($request->filled('reply')) {
                TicketHistory::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'description' => strip_tags($request->reply),
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]);
            }

            return response()->json([
                'code' => 200,
                'message' => __('admin.support.ticket_update_success'),
                'ticket' => $ticket
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.common.default_update_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function ticketUpdate(Request $request): JsonResponse
    {
        try {
            $user = Auth::guard('admin')->check() ? Auth::guard('admin')->user() : Auth::guard('web')->user();

            if (!$user) {
                return response()->json([
                    'code' => 401,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'ticketid' => 'required|exists:tickets,id',
                'status' => 'required|in:1,2,3,4',
                'reply' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) {
                        if (str_word_count($value) > 60) {
                            $fail(__('admin.support.reply_maxwords'));
                        }
                    },
                ],
            ], [
                'reply.required' => __('admin.support.reply_required'),
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => 422,
                    'errors' => $validator->errors()
                ], 422);
            }
           /** @var \Modules\Communication\Models\Ticket $ticket */
            $ticket = Ticket::findOrFail($request->ticketid);
            $statusChanged = ($ticket->status !== (int)$request->status);

            if ($statusChanged) {
                $currentStatus = (int)$ticket->status;
                $newStatus = (int)$request->status;

                $allowedTransitions = [
                    1 => [2],
                    2 => [3],
                    3 => [4],
                ];

                if (!isset($allowedTransitions[$currentStatus]) || !in_array($newStatus, $allowedTransitions[$currentStatus])) {
                    return response()->json([
                        'code' => 403,
                        'message' => __('admin.support.invalid_status_transition')
                    ], 403);
                }

                $ticket->status = $newStatus;
            }

            $ticket->updated_by = $user->id;
            $ticket->save();

            $finalStatus = (int)$ticket->status;
            if ($finalStatus !== 3) {
                return response()->json([
                    'code' => 403,
                    'message' => __('admin.support.reply_allowed_only_in_status_3')
                ], 403);
            }

            TicketHistory::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'description' => strip_tags($request->reply),
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            return response()->json([
                'code' => 200,
                'message' => __('admin.support.ticket_update_success'),
                'ticket' => $ticket
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.common.default_update_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $id = $request->id;
         /** @var \Modules\Communication\Models\Ticket|null $ticket */
            $ticket = Ticket::find($id);

            if (!$ticket) {
                return response()->json([
                    'code'    => 404,
                    'success' => false,
                    'message' => 'Contact not found.'
                ], 404);
            }

            $ticket->delete();

            return response()->json([
                'code'    => 200,
                'success' => true,
                'message' => __('admin.support.ticket_delete_success')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 500,
                'success' => false,
                'message' => __('admin.common.default_delete_error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
