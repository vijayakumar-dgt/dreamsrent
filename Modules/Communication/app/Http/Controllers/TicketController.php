<?php

namespace Modules\Communication\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Communication\Http\Requests\AddTicketRequest;
use Modules\Communication\Http\Requests\UpdateTicketRequest;
use Modules\Communication\Http\Requests\AssignTicketRequest;
use Modules\Communication\Models\TicketCategory;
use App\Models\User;
use Modules\Communication\Models\Ticket;
use Modules\Communication\Models\TicketHistory;
use Modules\Communication\Repositories\Contracts\TicketInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    protected TicketInterface $repository;

    public function __construct(TicketInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(): View
    {

        $users = User::whereIn('user_type', [1, 2])
            ->with('userDetail')
            ->get()
            ->map(function ($user) {
                $fullName = $user->name;

                if ($user->userDetail && $user->userDetail->first_name && $user->userDetail->last_name) {
                    $fullName = $user->userDetail->first_name . ' ' . $user->userDetail->last_name;
                }

                $user->full_name = ucwords($fullName);
                return $user;
            });

        return view('communication::ticket.index', compact('users'));
    }

    public function ticketDetails(): View
    {
        return view('communication::ticket.admin-ticket-details');
    }

    public function userTicket(): View
    {
        $seo_title = __('web.user.tickets');
        return view('communication::ticket.user-ticket', compact('seo_title'));
    }

    public function userTicketStore(AddTicketRequest $request): JsonResponse
    {
        try {
            $user = Auth::guard('admin')->check() ? Auth::guard('admin')->user() : Auth::guard('web')->user();

            if (!$user) {
                return response()->json([
                    'code' => 401,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            // Generate ticket ID
            $latestTicket = Ticket::latest('id')->first();
            $nextId = $latestTicket ? $latestTicket->id + 1 : 1;
            $ticketId = 'TICKET-' . str_pad((string) $nextId, 6, '0', STR_PAD_LEFT);

            // Handle file uploads
            $filePaths = [];

            if ($request->hasFile('document')) {
                foreach ($request->file('document') as $file) {
                    if ($file && $file->isValid()) {
                        $filePaths[] = $file->store('tickets', 'public');
                    }
                }
            }

            // Create ticket
            $ticket = $this->repository->create([
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

            $filters = [
                'ticketId' => $request->input('ticketId'),
                'priority' => $request->input('priority', []),
                'status' => $request->input('status', []),
                'sort_by' => $request->input('sort_by', 'latest'),
                'search' => $request->input('search', ''),
            ];

            $tickets = $this->repository->getTicketsForUser($user->id, $user->user_type, $filters);

            $tickets->transform(function ($ticket) {
                $ticket->formatted_created_at = formatDateTime($ticket->created_at, false);
                $ticket->formatted_updated_at = formatDateTime($ticket->updated_at, false);
                return $ticket;
            });

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

    public function ticketUpdateAsssign(AssignTicketRequest $request): JsonResponse
    {
        try {
            $user = Auth::guard('admin')->check() ? Auth::guard('admin')->user() : Auth::guard('web')->user();

            if (!$user) {
                return response()->json([
                    'code' => 401,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            $ticket = $this->repository->assignTicket(
                $request->ticketid,
                $request->assign_staff,
                $request->reply
            );

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

    public function ticketUpdate(UpdateTicketRequest $request): JsonResponse
    {
        try {
            $user = Auth::guard('admin')->check() ? Auth::guard('admin')->user() : Auth::guard('web')->user();

            if (!$user) {
                return response()->json([
                    'code' => 401,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            $ticket = $this->repository->updateStatus(
                (int)$request->ticketid,
                (int)$request->status,
                $request->reply
            );

            return response()->json([
                'code' => 200,
                'message' => __('admin.support.ticket_update_success'),
                'ticket' => $ticket
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
              'code' => $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500,
              'message' => $e->getMessage(),
            ], $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500);
        }
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $id = $request->input('id');
            if (!is_numeric($id)) {
                return response()->json([
                    'code' => 400,
                    'success' => false,
                    'message' => 'Invalid ticket ID format'
                ], 400);
            }

            $result = $this->repository->delete((int)$id);

            if (!$result) {
                return response()->json([
                    'code'    => 404,
                    'success' => false,
                    'message' => 'Ticket not found.'
                ], 404);
            }

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
