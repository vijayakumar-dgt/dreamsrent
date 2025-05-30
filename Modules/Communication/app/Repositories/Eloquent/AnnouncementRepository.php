<?php

namespace Modules\Communication\Repositories\Eloquent;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\CarInfo\Models\Category;
use Modules\CarInfo\Models\Season;
use Modules\CarInfo\Repositories\Contracts\CategoryRepositoryInterface;
use Modules\Communication\Models\Announcement;
use Modules\Communication\Repositories\Contracts\AnnouncementRepositoryInterface;

class AnnouncementRepository implements AnnouncementRepositoryInterface
{
    public function store(Request $request): array
    {
        try {
            $data = $request->only([
                'announcement_title',
                'user_type',
                'description',
                'announcement_type',
                'status',
            ]);
            $typeMapping = [
                'general'   => 1,
                'important' => 2,
                'urgent'    => 3,
            ];

            $typeInput = $data['announcement_type'] ?? 'general';
            $data['announcement_type'] = $typeMapping[$typeInput] ?? 1;

            if (!isset($data['status'])) {
                $data['status'] = 1;
            }

            if ($request->id) {
                $announcement = Announcement::find($request->id);

                if (!$announcement) {
                    return [
                        'code' => 404,
                        'success' => false,
                        'message' => __('admin.support.announcement_not_found'),
                    ];
                }

                $announcement->update($data);

                return [
                    'code' => 200,
                    'success' => true,
                    'message' => __('admin.support.announcement_update_success'),
                    'data' => $announcement,
                ];
            }

            $announcement = Announcement::create($data);

            return [
                'code' => 200,
                'success' => true,
                'message' => __('admin.support.announcement_create_success'),
                'data' => $announcement,
            ];
        } catch (\Exception $e) {
            return [
                'code' => 500,
                'success' => false,
                'message' => __('admin.common.default_update_error'),
                'error' => $e->getMessage(),
            ];
        }
    }

    public function list(Request $request): array
    {
        try {
            if ($request->input('id')) {
                $announcement = Announcement::select('announcements.*')
                    ->where('announcements.id', $request->id)
                    ->first();

                return [
                    'code' => 200,
                    'success' => true,
                    'message' => __('admin.common.default_retrieve_success'),
                    'data' => $announcement,
                ];
            }

            $announcements = Announcement::select('announcements.*')
                ->when($request->input('user_type'), fn($q, $userType) => $q->where('announcements.user_type', $userType))
                ->when($request->input('status') !== null && $request->status !== 'all', fn($q) => $q->where('announcements.status', $request->status))
                ->when($request->input('title'), fn($q, $title) => $q->where('announcements.announcement_title', 'like', '%' . $title . '%'))
                ->when($request->input('sort'), function ($query, $sort) {
                    switch ($sort) {
                        case 'ascending':
                            $query->orderBy('announcements.announcement_title', 'asc');
                            break;
                        case 'descending':
                            $query->orderBy('announcements.announcement_title', 'desc');
                            break;
                        case 'last_month':
                            $startDate = Carbon::now()->subMonth()->startOfMonth();
                            $endDate = Carbon::now()->subMonth()->endOfMonth();
                            $query->whereBetween('announcements.created_at', [$startDate, $endDate]);
                            break;
                        case 'last_7_days':
                            $start = Carbon::now()->subDays(7)->startOfDay();
                            $end = Carbon::now()->endOfDay();
                            $query->whereBetween('announcements.created_at', [$start, $end]);
                            break;
                        default:
                            $query->orderBy('announcements.created_at', 'desc');
                    }
                })
                ->get();

            $announcements->transform(function ($item) {
                $item->formatted_created_at = formatDateTime($item->created_at, false);
                return $item;
            });

            return [
                'code' => 200,
                'success' => true,
                'message' => __('admin.common.default_retrieve_success'),
                'data' => $announcements,
            ];
        } catch (\Exception $e) {
            return [
                'code' => 500,
                'success' => false,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage(),
            ];
        }
    }

    public function delete(Request $request): array
    {
        try {
            $id = $request->id;
            $announcement = Announcement::find($id);

            if (!$announcement) {
                return [
                    'code' => 404,
                    'success' => false,
                    'message' => __('admin.support.announcement_not_found'),
                ];
            }

            $announcement->delete();

            return [
                'code' => 200,
                'success' => true,
                'message' => __('admin.support.announcement_delete_success'),
            ];
        } catch (\Exception $e) {
            return [
                'code' => 500,
                'success' => false,
                'message' => __('admin.common.default_delete_error'),
                'error' => $e->getMessage(),
            ];
        }
    }

}