<?php

namespace Modules\Communication\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Communication\Models\Contact;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('communication::contact-message.index');
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:contacts,email',
                'phone_number' => 'required|string|max:255',
                'message' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $imagePath = null;
            if ($request->hasFile('image') && $request->file('image') !== null) {
                $imagePath = $request->file('image')->store('contacts', 'public');
            }

            $contact = Contact::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'message' => $request->message,
                'image' => $imagePath,
            ]);

            return response()->json([
                'code' => 200,
                'success' => true,
                'message' => __('admin.support.contact_message_create_success'),
                'data' => $contact,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Contact creation failed: ' . $e->getMessage());

            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => __('admin.common.default_create_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function list(Request $request): JsonResponse
    {
        try {
            $sortBy = $request->get('sort_by', 'latest');
            $search = $request->get('search', '');

            $contacts = Contact::query()
                ->when($search, function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                          ->orWhere('phone_number', 'LIKE', "%{$search}%")
                          ->orWhere('email', 'LIKE', "%{$search}%");
                })
                ->when($sortBy === 'latest', fn($query) => $query->orderBy('created_at', 'desc'))
                ->when($sortBy === 'ascending', fn($query) => $query->orderBy('name', 'asc'))
                ->when($sortBy === 'descending', fn($query) => $query->orderBy('name', 'desc'))
                ->when($sortBy === 'last_month', fn($query) => $query->whereBetween('created_at', [now()->subMonth(), now()]))
                ->when($sortBy === 'last_7_days', fn($query) => $query->whereBetween('created_at', [now()->subDays(7), now()]))
                ->get()
                ->map(function ($contact) {
                    $url = uploadedAsset($contact->image, 'profile');
                    $contact->image = $url;
                    return $contact;
                });
            return response()->json([
                'code' => 200,
                'success' => true,
                'message' => __('admin.common.default_retrieve_success'),
                'data' => $contacts,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Fetching contacts failed: ' . $e->getMessage());

            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $id = (int) $request->id;
            $contact = Contact::find($id);

            if (!$contact) {
                return response()->json([
                    'code'    => 404,
                    'success' => false,
                    'message' => 'Contact not found.'
                ], 404);
            }

            $contact->delete();

            return response()->json([
                'code'    => 200,
                'success' => true,
                'message' => __('admin.support.contact_message_delete_success')
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
