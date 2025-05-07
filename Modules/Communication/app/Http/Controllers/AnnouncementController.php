<?php

namespace Modules\Communication\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Communication\Models\Announcement;
use Modules\Communication\Models\Enquiry;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $announcement_types = DB::table('announcement_types')->get(['id', 'name']);
        return view('communication::announcement.index', compact('announcement_types'));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'announcement_title' => 'required|string|max:100',
            'announcement_type' => 'required|exists:announcement_types,id',
            'user_type' => 'required|in:user,admin',
            'description' => 'required|string|max:500',
        ]);

        try {
            if ($request->id) {
                $announcement = Announcement::find($request->id);

                if (!$announcement) {
                    return response()->json([
                        'code' => 404,
                        'success' => false,
                        'message' => 'Announcement not found!'
                    ], 404);
                }
                /** @var \Modules\Communication\Models\Announcement $announcement */
                $announcement->update($request->all());

                return response()->json([
                    'code' => 200,
                    'success' => true,
                    'message' => __('admin.support.announcement_create_success'),
                    'data' => $announcement
                ]);
            } else {
                $announcement = Announcement::create($request->all());

                return response()->json([
                    'code' => 200,
                    'success' => true,
                    'message' => __('admin.support.announcement_update_success'),
                    'data' => $announcement
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => __('admin.common.default_update_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function list(Request $request): JsonResponse
    {
        try {
            if ($request->input('id')) {
                $announcement = Announcement::select('announcements.*', 'announcement_types.name as type_name')
                    ->join('announcement_types', 'announcements.announcement_type', '=', 'announcement_types.id')
                    ->where('announcements.id', $request->id)
                    ->first();

                return response()->json([
                    'code' => 200,
                    'success' => true,
                    'message' => __('admin.common.default_retrieve_success'),
                    'data' => $announcement
                ]);
            }

            $announcements = Announcement::select('announcements.*', 'announcement_types.name as type_name')
                ->join('announcement_types', 'announcements.announcement_type', '=', 'announcement_types.id')
                ->when($request->input('user_type'), function ($query, $userType) {
                    $query->where('announcements.user_type', $userType);
                })
                ->when($request->input('announcement_type'), function ($query, $announcementType) {
                    $query->where('announcements.announcement_type', $announcementType);
                })
                ->when($request->input('title'), function ($query, $title) {
                    $titleString = is_string($title) ? $title : '';
                    $query->where('announcements.announcement_title', 'like', '%' . $titleString . '%');                })
                ->orderBy('announcements.created_at', 'desc')
                ->get();

            return response()->json([
                'code' => 200,
                'success' => true,
                'message' => __('admin.common.default_retrieve_success'),
                'data' => $announcements
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $id = $request->id;
            $enquiry = Announcement::find($id);

            if (!$enquiry) {
                return response()->json([
                    'code'    => 404,
                    'success' => false,
                    'message' => 'Announcement not found.'
                ], 404);
            }
            /** @var \Modules\Communication\Models\Announcement $enquiry */
            $enquiry->delete();

            return response()->json([
                'code'    => 200,
                'success' => true,
                'message' => __('admin.support.announcement_delete_success')
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
