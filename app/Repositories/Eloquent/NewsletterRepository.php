<?php

namespace App\Repositories\Eloquent;

use App\Models\NewsletterSubscriber;
use App\Models\UserDetail;
use App\Repositories\Contracts\NewsLetterRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\GeneralSetting\Models\GeneralSetting;

class NewsLetterRepository implements NewsLetterRepositoryInterface
{
    public function save(Request $request): array
    {
        try {
            NewsletterSubscriber::create([
                'email' => $request->subscriber_email
            ]);

            try {
                $ownerName = GeneralSetting::where('key', 'owner_name')->value('value');
                $notifyData = [
                    'owner_name' => $ownerName ?? 'Admin',
                ];
                sendNewsletterEmail($request->subscriber_email, 'newsletter', $notifyData);
            } catch (\Exception $e) {
            }

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('web.user.newsletter_subscriber_create_success')
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('web.common.default_create_error'),
                'error'   => $e->getMessage()
            ];
        }
    }

    public function list(Request $request): array
    {
        try {
            $columnIndex = $request->order[0]['column'] ?? 0;
            $columnName = $request->columns[$columnIndex]['data'] ?? 'email';
            $orderDir = $request->order[0]['dir'] ?? 'asc';

            $query = NewsletterSubscriber::select('id', 'email', 'created_at');

            // Search
            if (!empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('email', 'like', "%{$search}%");
                });
            }

            // Sort Filters
            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch (strtolower($request->sort_by)) {
                    case 'latest':
                        $query->orderBy('newsletter_subscribers.created_at', 'desc');
                        break;
                    case 'ascending':
                        $query->orderBy('newsletter_subscribers.email', 'asc');
                        break;
                    case 'descending':
                        $query->orderBy('newsletter_subscribers.email', 'desc');
                        break;
                    case 'last month':
                        $startDate = Carbon::now()->subMonth()->startOfMonth();
                        $endDate = Carbon::now()->subMonth()->endOfMonth();
                        $query->whereBetween('newsletter_subscribers.created_at', [$startDate, $endDate]);
                        break;
                    case 'last 7 days':
                        $startDate = Carbon::now()->subDays(7)->startOfDay();
                        $endDate = Carbon::now()->endOfDay();
                        $query->whereBetween('newsletter_subscribers.created_at', [$startDate, $endDate]);
                        break;
                }
            }

            // Final ordering
            $query->orderBy($columnName, $orderDir);

            $totalRecords = $query->count();
            $filteredRecords = $query->count();

            // Pagination
            $query->offset($request->start ?? 0)->limit($request->length ?? 10);

            $subscribers = $query->get()->map(function ($item) {
                $item->created_date = formatDateTime($item->created_at, false); // Format helper
                return $item;
            });

            return [
                'status'          => 'success',
                'code'            => 200,
                'draw'            => intval($request->draw),
                'recordsTotal'    => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data'            => $subscribers,
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('web.common.default_retrieve_error'),
                'error'   => $e->getMessage()
            ];
        }
    }

    public function delete(Request $request): array
    {
        try {
            $id = $request->id;

            $deleted = NewsletterSubscriber::where('id', $id)->delete();

            if ($deleted) {
                return [
                    'status'  => 'success',
                    'code'    => 200,
                    'message' => __('admin.others.newsletter_delete_success')
                ];
            }

            return [
                'status'  => 'error',
                'code'    => 404,
                'message' => __('admin.common.not_found')
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_delete_error'),
                'error'   => $e->getMessage()
            ];
        }
    }

    public function sendNewsletter(Request $request): array
    {
        try {
            $email = $request->email;

            $user = Auth::guard('admin')->user();
            $userId = $user->id ?? null;

            $userDetail = UserDetail::where('user_id', $userId)->first();
            $name = $userDetail
                ? trim($userDetail->first_name . ' ' . $userDetail->last_name)
                : 'Admin';

            $notifyData = [
                'user_name' => $name,
            ];

            sendNewsletterEmail($email, 'newsletter', $notifyData);

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.others.newsletter_send_success')
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.others.newsletter_send_error'),
                'error'   => $e->getMessage()
            ];
        }
    }
}
