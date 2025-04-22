<?php

namespace Modules\GeneralSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\GeneralSetting\Models\SignatureSetting;
use Illuminate\Support\Facades\Artisan;


class SignatureSettingsController extends Controller
{
    public function signature()
    {
        return view('generalsetting::app_settings.signature-setting');
    }

    public function clearCache()
    {
        return view('generalsetting::other_settings.clear-cache');
    }

    public function clear(Request $request)
    {
        try {
            Artisan::call('optimize:clear');

            return response()->json([
                'code' => 200,
                'message' => __('admin.general_settings.cache_cleared_successfully')
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.general_settings.cache_clear_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Store a new signature
    public function store(Request $request)
    {
        try {
            $request->validate([
                'signature_name' => 'required|string|max:255',
                'signature_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
                'is_default' => 'nullable|boolean',
            ]);

            if ($request->hasFile('signature_image')) {
                $imagePath = $request->file('signature_image')->store('signatures', 'public');
            }

            if ($request->is_default) {
                SignatureSetting::where('is_default', 1)->update(['is_default' => 0]);
            }

            $signature = SignatureSetting::create([
                'signature_name' => $request->signature_name,
                'signature_image' => $imagePath ?? null,
                'status' => 1, // Default to active
                'is_default' => $request->is_default ? 1 : 0,
            ]);

            $totalRecords = SignatureSetting::count();

            return response()->json([
                'code' => 200,
                'message' =>  __('admin.general_settings.signature_success'),
                'data' => $signature,
                'totalRecords' => $totalRecords
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' =>  __('admin.general_settings.retrive_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|integer|exists:signature_settings,id',
                'signature_name' => 'required|string|max:255',
                'signature_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
                'is_default' => 'nullable|boolean',
                'status' => 'nullable|boolean'
            ]);

            $signature = SignatureSetting::find($request->id);
            if (!$signature) {
                return response()->json([
                    'code' => 404,
                    'message' =>  __('admin.general_settings.signature_error')
                ], 404);
            }

            if ($request->hasFile('signature_image')) {
                if ($signature->signature_image && Storage::disk('public')->exists($signature->signature_image)) {
                    Storage::disk('public')->delete($signature->signature_image);
                }

                $imagePath = $request->file('signature_image')->store('signatures', 'public');
                $signature->signature_image = $imagePath;
            }

            if ($request->is_default) {
                SignatureSetting::where('is_default', 1)->update(['is_default' => 0]);
            }

            $signature->update([
                'signature_name' => $request->signature_name,
                'is_default' => $request->is_default ? 1 : 0,
                'status' => $request->status ? 1 : 0
            ]);

            return response()->json([
                'code' => 200,
                'message' => __('admin.general_settings.signature_update_success'),
                'data' => $signature
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'message' =>__('admin.general_settings.retrive_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $search = $request->input('search');

            $signatures = SignatureSetting::when($search, function ($query) use ($search) {
                    $query->where('signature_name', 'like', "%{$search}%");
                })
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($signature) {
                    $signature->signature_image = $signature->signature_image
                        ? asset('storage/' . $signature->signature_image)
                        : null;

                    return $signature;
                });

            return response()->json([
                'code' => 200,
                'message' =>__('admin.general_settings.signature_list_fetch_success'),
                'data' => $signatures,
                'totalRecords' => $signatures->count()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.general_settings.fail_signature_list'),
                'error' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|integer|exists:signature_settings,id'
            ]);

            $signature = SignatureSetting::findOrFail($request->id);
            $signature->delete();

            return response()->json([
                'code' => 200,
                'message' => __('admin.general_settings.signature_deleted_successfully'),
                'totalRecords' => SignatureSetting::count()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' =>__('admin.general_settings.fail_delete_signature'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
