<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ReviewMessages;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use stdClass;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Modules\Booking\Models\Booking;

class ReviewController extends Controller
{
    protected ?Authenticatable $authUser;
    public function __construct()
    {
        $this->authUser = current_user();
    }

    public function addreview(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'comments' => [
                'required',
                'min:3',
            ],
        ], [
            'comments.required' => __('web.home.comments_required'),
            'comments.min' => __('web.home.comments_minlength'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $userId = $this->authUser->id ?? $request->user_id;
        $vehicleId = $request->vehicle_id;

        $booking = Booking::where('vehicle_id', $vehicleId)
            ->where('customer_id', $userId)
            ->where('booking_status', 5)
            ->first();

        if (!$booking) {
            return response()->json([
                'status' => 'error',
                'code' => 403,
                'message' => __('web.home.review_not_allowed')
            ], 403);
        }

        try {
            $data = [
                'vehicle_id' => $request->vehicle_id,
                'user_id' => $this->authUser->id ?? $request->user_id,
                'service_ratings' => $request->service_ratings ?? 0,
                'location_ratings' => $request->location_ratings ?? 0,
                'facility_ratings' => $request->facility_ratings ?? 0,
                'value_for_money_ratings' => $request->value_for_money_ratings ?? 0,
                'cleanliness_ratings' => $request->cleanliness_ratings ?? 0,
            ];
            $totalRatings = $data['service_ratings'] +
                $data['location_ratings'] + $data['facility_ratings'] + $data['value_for_money_ratings'] + $data['cleanliness_ratings'];
            $data['average_ratings'] = $totalRatings / 5;

            $reviews = Review::create($data);

            ReviewMessages::create([
                'review_id' => $reviews->id,
                'user_id' => $this->authUser->id ?? $request->user_id,
                'comments' => $request->comments,
            ]);

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => __('web.home.review_create_success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => __('web.common.default_create_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function addReply(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reply_comments' => [
                'required',
                'min:3',
            ],
        ], [
            'reply_comments.required' => __('web.home.reply_comments_required'),
            'reply_comments.min' => __('web.home.reply_comments_minlength'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $userId = $this->authUser->id ?? $request->user_id;
        $vehicleId = $request->vehicle_id;

        $booking = Booking::where('vehicle_id', $vehicleId)
            ->where('customer_id', $userId)
            ->where('booking_status', 5)
            ->first();

        if (!$booking) {
            return response()->json([
                'status' => 'error',
                'code' => 403,
                'message' => __('web.home.reply_not_allowed')
            ], 403);
        }

        try {
            ReviewMessages::create([
                'parent_id' => $request->review_id,
                'user_id' => $this->authUser->id ?? $request->user_id,
                'comments' => $request->reply_comments,
            ]);

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => __('web.home.reply_create_success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => __('web.home.reply_create_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function reviewsList(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required',
        ], [
            'vehicle_id.required' => __('web.home.vehicle_id_required'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        try {
            $vehicleId = $request->vehicle_id ?? null;

            $reviewsData = Review::select(
                'reviews.id',
                'reviews.vehicle_id',
                'reviews.user_id',
                'reviews.average_ratings',
                'review_messages.comments',
                'review_messages.likes',
                'review_messages.dislikes',
                'users.name as user_name',
                DB::raw("CONCAT(user_details.first_name, ' ', user_details.last_name) as full_name"),
                'user_details.profile_image',
                'reviews.created_at',
            )
                ->join('review_messages', 'review_messages.review_id', '=', 'reviews.id')
                ->join('users', 'users.id', '=', 'reviews.user_id')
                ->leftJoin('user_details', 'user_details.user_id', '=', 'reviews.user_id')
                ->where('reviews.vehicle_id', $vehicleId)
                ->where('review_messages.parent_id', 0)
                ->orderBy('reviews.id', 'desc')
                ->get()->map(function ($review) {
                    $review->profile_image = is_string($review->profile_image) || is_null($review->profile_image)
                        ? uploadedAsset($review->profile_image, 'profile')
                        : uploadedAsset(null, 'profile');
                    $review->review_date = formatDateTime($review->created_at, false);
                    $review->user_name = ucfirst((string) $review->user_name);
                    $review->full_name = ucfirst((string) $review->full_name);
                    unset($review->created_at);
                    $review->replies = $this->fetchReviewReplies($review->id);
                    return $review;
                });

            $serviceRatings = Review::where('vehicle_id', $vehicleId)->avg('service_ratings');
            $locationRatings = Review::where('vehicle_id', $vehicleId)->avg('location_ratings');
            $facilityRatings = Review::where('vehicle_id', $vehicleId)->avg('facility_ratings');
            $valueForMoneyRatings = Review::where('vehicle_id', $vehicleId)->avg('value_for_money_ratings');
            $cleanlinessRatings = Review::where('vehicle_id', $vehicleId)->avg('cleanliness_ratings');
            $overallRatings = Review::where('vehicle_id', $vehicleId)->avg('average_ratings');
            $totalReviews = Review::where('vehicle_id', $vehicleId)->count();

            $servicePercentage = number_format(($serviceRatings / 5) * 100, 0);
            $locationPercentage = number_format(($locationRatings / 5) * 100, 0);
            $facilityPercentage = number_format(($facilityRatings / 5) * 100, 0);
            $valueForMoneyPercentage = number_format(($valueForMoneyRatings / 5) * 100, 0);
            $cleanlinessPercentage = number_format(($cleanlinessRatings / 5) * 100, 0);

            $finalData = [
                'reviews_meta' => [
                    'avg_service_ratings' => number_format((float) $serviceRatings, 1),
                    'service_ratings_percentage' => $servicePercentage . '%',
                    'avg_location_ratings' => number_format((float) $locationRatings, 1),
                    'location_ratings_percentage' => $locationPercentage . '%',
                    'avg_facility_ratings' => number_format((float) $facilityRatings, 1),
                    'facility_ratings_percentage' => $facilityPercentage . '%',
                    'avg_value_for_money_ratings' => number_format((float) $valueForMoneyRatings, 1),
                    'value_for_money_ratings_percentage' => $valueForMoneyPercentage . '%',
                    'avg_cleanliness_ratings' => number_format((float) $cleanlinessRatings, 1),
                    'cleanliness_ratings_percentage' => $cleanlinessPercentage . '%',
                    'overall_avg_ratings' => number_format((float) $overallRatings, 1),
                    'overall_ratings_percentage' => round(((float) $overallRatings / 5) * 100, 1) . '%',
                    'rating_description' => $totalReviews > 0 ? $this->getRatingDescription($overallRatings) : '',
                    'total_reviews' => $totalReviews,
                ],
                'reviews' => $reviewsData
            ];

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'data' => $finalData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => __('web.common.default_retrieve_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch review replies.
     *
     * @param int|null $reviewId
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\ReviewMessages>
     */
    function fetchReviewReplies(?int $reviewId): Collection
    {
        $replies = ReviewMessages::select(
            'review_messages.comments',
            'review_messages.likes',
            'review_messages.dislikes',
            'users.name as user_name',
            DB::raw("CONCAT(user_details.first_name, ' ', user_details.last_name) as full_name"),
            'user_details.profile_image',
            'review_messages.created_at',
        )
            ->join('users', 'users.id', '=', 'review_messages.user_id')
            ->leftJoin('user_details', 'user_details.user_id', '=', 'review_messages.user_id')
            ->where('review_messages.parent_id', $reviewId)
            ->get()->map(function ($reply) {
                $reply->profile_image = is_string($reply->profile_image) || is_null($reply->profile_image)
                    ? uploadedAsset($reply->profile_image, 'profile')
                    : uploadedAsset(null, 'profile');
                $reply->reply_date = formatDateTime($reply->created_at, false);
                $reply->full_name = ucfirst((string) $reply->full_name);
                $reply->user_name = ucfirst((string) $reply->user_name);
                unset($reply->created_at);
                return $reply;
            });
        return $replies;
    }

    function getRatingDescription(mixed $rating): string
    {
        if ($rating >= 4.5) {
            return __('web.home.excellent');
        } elseif ($rating >= 4.0) {
            return __('web.home.very_good');
        } elseif ($rating >= 3.5) {
            return __('web.home.good');
        } elseif ($rating >= 3.0) {
            return __('web.home.average');
        } elseif ($rating >= 2.0) {
            return __('web.home.below_average');
        } else {
            return __('web.home.poor');
        }
    }

    public function userReviewsList(Request $request): JsonResponse
    {
        try {
            $userId = $this->authUser->id ?? $request->user_id;

            $columnIndex = $request->order[0]['column'] ?? 0;
            $columnName = $request->columns[$columnIndex]['data'] ?? 'vehicle_name';
            $orderDir = $request->order[0]['dir'] ?? 'asc';

            $query = Review::select(
                'reviews.id',
                'reviews.vehicle_id',
                'reviews.user_id',
                'reviews.average_ratings',
                'review_messages.comments',
                'vehicle_info.name as vehicle_name',
                'vehicle_info.vehicle_image',
                'reviews.created_at',
            )
                ->join('review_messages', 'review_messages.review_id', '=', 'reviews.id')
                ->join('vehicle_info', 'reviews.vehicle_id', '=', 'vehicle_info.id')
                ->where('reviews.user_id', $userId)
                ->where('review_messages.parent_id', 0);

            if ($request->has('duration') && $request->duration != "") {
                $customFrom = $request->custom_from_date ?? "";
                $customTo = $request->custom_to_date ?? "";
                $duration = $this->getDuration($request->duration, $customFrom, $customTo);

                if (!isset($duration['error']) && isset($duration['from'])) {
                    $query->whereBetween('reviews.created_at', [$duration['from'], $duration['to']]);
                }
            }

            if ($request->has('sort_by') && $request->sort_by != "") {
                switch ($request->sort_by) {
                    case 'asc':
                        $query->orderBy('reviews.created_at', 'asc');
                        break;
                    case 'desc':
                        $query->orderBy('reviews.created_at', 'desc');
                        break;
                    case 'alphabet':
                        $query->orderByRaw("LOWER(CONCAT_WS(' ', vehicle_info.name)) asc");
                        break;
                }
            }

            if ($columnName === 'vehicle_name') {
                $query->orderByRaw("LOWER(CONCAT_WS(' ', vehicle_info.name)) {$orderDir}");
            } else {
                $query->orderBy($columnName, $orderDir);
            }

            $totalRecords = $query->count();
            $filteredRecords = $query->count();

            $query->offset($request->start)->limit($request->length);

            $reviews = $query->get()->map(function ($item) {
                $item->vehicle_image = is_string($item->vehicle_image) || is_null($item->vehicle_image)
                    ? uploadedAsset($item->vehicle_image, 'profile')
                    : uploadedAsset(null, 'profile');
                return $item;
            });

            return response()->json([
                "draw" => intval($request->draw),
                "recordsTotal" => $totalRecords,
                "recordsFiltered" => $filteredRecords,
                "data" => $reviews,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => __('web.common.default_retrieve_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @return array{from: string, to: string}|array{error: string}
     */
    public function getDuration(?string $duration, ?string $customFromDate = null, ?string $customToDate = null): array
    {
        switch ($duration) {
            case 'this_week':
                $duration = [
                    'from' => date('Y-m-d 00:00:00', strtotime('monday this week')),
                    'to' => date('Y-m-d 23:59:59', strtotime('sunday this week'))
                ];
                break;
            case 'this_month':
                $duration = [
                    'from' => date('Y-m-01 00:00:00'),
                    'to' => date('Y-m-t 23:59:59')
                ];
                break;
            case 'last30':
                $duration = [
                    'from' => date('Y-m-d 00:00:00', strtotime('-30 days')),
                    'to' => date('Y-m-d 23:59:59')
                ];
                break;
            case 'last60':
                $duration = [
                    'from' => date('Y-m-d 00:00:00', strtotime('-60 days')),
                    'to' => date('Y-m-d 23:59:59')
                ];
                break;
            case 'last7':
                $duration = [
                    'from' => date('Y-m-d 00:00:00', strtotime('-7 days')),
                    'to' => date('Y-m-d 23:59:59')
                ];
                break;
            case 'custom':
                if (!empty($customFromDate) && !empty($customToDate)) {
                    if (strtotime($customFromDate) > strtotime($customToDate)) {
                        return ['error' => 'Custom from date cannot be greater than to date'];
                    }

                    $fromTimestamp = strtotime($customFromDate);
                    $toTimestamp = strtotime($customToDate);

                    if ($fromTimestamp === false || $toTimestamp === false) {
                        return ['error' => 'Invalid custom date format'];
                    }

                    if ($fromTimestamp > $toTimestamp) {
                        return ['error' => 'Custom from date cannot be greater than to date'];
                    }

                    $duration = [
                        'from' => date('Y-m-d', $fromTimestamp) . ' 00:00:00',
                        'to' => date('Y-m-d', $toTimestamp) . ' 23:59:59'
                    ];
                } else {
                    return ['error' => 'Custom dates are required'];
                }
                break;
            default:
                $duration = ['error' => 'Invalid duration specified'];
        }

        return $duration;
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $id = $request->id;
            Review::where('id', $id)->delete();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => __('web.user.review_delete_success')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => __('web.common.default_delete_error')
            ], 500);
        }
    }

    public function adminReviews(Request $request): View
    {
        return view('admin.reviews');
    }

    public function adminReviewsList(Request $request): JsonResponse
    {
        try {
            $userId = $this->authUser->id ?? $request->user_id;

            $columnIndex = $request->order[0]['column'] ?? 0;
            $columnName = $request->columns[$columnIndex]['data'] ?? 'vehicle_name';
            $orderDir = $request->order[0]['dir'] ?? 'asc';

            $query = Review::select(
                'reviews.id',
                'reviews.vehicle_id',
                'reviews.user_id',
                'reviews.average_ratings',
                'review_messages.comments',
                'vehicle_info.name as vehicle_name',
                'vehicle_info.vehicle_image',
                'reviews.created_at',
                'user_details.profile_image',
                DB::raw("CONCAT(user_details.first_name, ' ', user_details.last_name) as customer_full_name"),
            )
                ->join('users', 'users.id', '=', 'reviews.user_id')
                ->leftJoin('user_details', 'user_details.user_id', '=', 'reviews.user_id')
                ->join('review_messages', 'review_messages.review_id', '=', 'reviews.id')
                ->join('vehicle_info', 'reviews.vehicle_id', '=', 'vehicle_info.id')
                ->where('review_messages.parent_id', 0);

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('users.name', 'LIKE', "%{$search}%")
                        ->orWhere('users.email', 'LIKE', "%{$search}%")
                        ->orWhere('users.phone_number', 'LIKE', "%{$search}%")
                        ->orWhere('user_details.first_name', 'LIKE', "%{$search}%")
                        ->orWhere('user_details.last_name', 'LIKE', "%{$search}%")
                        ->orWhere('vehicle_info.name', 'LIKE', "%{$search}%");
                });
            }

            if ($request->has('sort_by_date') && !empty($request->sort_by_date)) {
                $dates = explode(' - ', $request->sort_by_date);
                if (count($dates) === 2) {
                    $startDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[0]));
                    $endDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[1]));

                    if ($startDate && $endDate) {
                        $query->whereBetween('reviews.created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
                    }
                }
            }

            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch (strtolower($request->sort_by)) {
                    case 'latest':
                        $query->orderBy('reviews.created_at', 'desc');
                        break;
                    case 'ascending':
                        $query->orderBy('vehicle_info.name', 'asc');
                        break;
                    case 'descending':
                        $query->orderBy('vehicle_info.name', 'desc');
                        break;
                    case 'last month':
                        $startDate = \Carbon\Carbon::now()->subMonth()->startOfMonth();
                        $endDate = \Carbon\Carbon::now()->subMonth()->endOfMonth();
                        $query->whereBetween('reviews.created_at', [$startDate, $endDate]);
                        break;
                    case 'last 7 days':
                        $startDate = \Carbon\Carbon::now()->subDays(7)->startOfDay();
                        $endDate = \Carbon\Carbon::now()->endOfDay();
                        $query->whereBetween('reviews.created_at', [$startDate, $endDate]);
                        break;
                }
            }

            if ($columnName === 'vehicle_name') {
                $query->orderByRaw("LOWER(CONCAT_WS(' ', vehicle_info.name)) {$orderDir}");
            } elseif ($columnName === 'customer_full_name') {
                $query
                    ->orderByRaw("LOWER(CONCAT_WS(' ', user_details.first_name, user_details.last_name)) {$orderDir}");
            } elseif ($columnName === 'review_date') {
                $query->orderBy('reviews.created_at', $orderDir);
            } else {
                $query->orderBy($columnName, $orderDir);
            }

            $totalRecords = Review::count();
            $filteredRecords = $query->count();

            $query->offset($request->start)->limit($request->length);

            $reviews = $query->get()->map(function ($item) {
                $item->vehicle_image = is_string($item->vehicle_image) || is_null($item->vehicle_image)
                    ? uploadedAsset($item->vehicle_image, 'default')
                    : uploadedAsset(null, 'default');
                $item->profile_image = is_string($item->profile_image) || is_null($item->profile_image)
                    ? uploadedAsset($item->profile_image, 'profile')
                    : uploadedAsset(null, 'profile');
                $item->review_date = formatDateTime($item->created_at, false);

                unset($item->created_at);
                return $item;
            });

            return response()->json([
                "draw" => intval($request->draw),
                "recordsTotal" => $totalRecords,
                "recordsFiltered" => $filteredRecords,
                "data" => $reviews,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => __('web.common.default_retrieve_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
