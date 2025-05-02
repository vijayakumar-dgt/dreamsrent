<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Booking\Models\Booking;
use Modules\GeneralSetting\Models\Currency;
use Modules\CarInfo\Models\VehicleInfo;
use App\Models\User;
use App\Models\Invoice;
use Modules\GeneralSetting\Models\GeneralSetting;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(): View
    {
        $invoices = Invoice::with('items')
            ->leftJoin('users', 'invoices.customer_id', '=', 'users.id')
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->select('invoices.*', 'users.name', 'users.email', 'user_details.profile_image')
            ->where('invoices.deleted_at', null)->get();



        return view("admin.invoice.index", compact('invoices'));
    }

    public function addInvoice(): View
    {
        $authId = current_user();
        $languageId = $authId->language_id ?? null;
        $cars = VehicleInfo::where('status', 1)->where('deleted_at', null)->where('language_id', $languageId)->get();
        $currencies = Currency::where('status', 1)->where('deleted_at', null)->get();
        $users = User::where('status', 1)->where('deleted_at', null)->get();
        $currentUser = Auth::user();
        $payments = GeneralSetting::where('group_id', 13)->where('value', 1)->get();
        $generalSettings = GeneralSetting::where('group_id', 5)->where('key', 'currency')->first();

        $currency = $generalSettings ? DB::table('currencies')->where('id', $generalSettings->value)->select('symbol')->first() : null;
        $symbol = $currency->symbol ?? '$';

        $bookings = Booking::Join('users', 'bookings.customer_id', '=', 'users.id')
        ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
        ->leftJoin('vehicle_info', 'bookings.vehicle_id', '=', 'vehicle_info.id')
        ->select(
            'bookings.*',
            'users.name as customer',
            'user_details.profile_image',
            'vehicle_info.vehicle_image',
            'vehicle_info.name as vehicle'
        )
        ->whereDate('start_datetime', '>=', Carbon::today())
        ->orderBy('start_datetime', 'asc')->get();

        return view(
            "admin.invoice.add-invoice",
            compact('cars', 'currencies', 'users', 'currentUser', 'payments', 'symbol', 'bookings')
        );
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'car_id' => 'required',
            'currency_id' => 'required',
            'status' => 'required|string',
            'biller' => 'required|string',
            'customer_id' => 'required',
            'payment_method' => 'required|string',
            'terms' => 'required|string',
            'notes' => 'required|string',
            'items' => 'required|array',
            'items.*.description' => 'required|string',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.total_price' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric',
            'grand_total' => 'required|numeric',
        ], [
            'car_id.required' => __('admin.finance_accounts.vehicle_required'),
            'currency_id.required' => __('admin.finance_accounts.currency_required'),
            'status.required' => __('admin.finance_accounts.status_required'),
            'biller.required' => __('admin.finance_accounts.biller_required'),
            'customer_id.required' => __('admin.finance_accounts.customer_required'),
            'payment_method.required' => __('admin.finance_accounts.payment_method_required'),
            'terms.required' => __('admin.finance_accounts.terms_condition_required'),
            'notes.required' => __('admin.finance_accounts.notes_required'),
            'items.required' => __('admin.finance_accounts.items_required'),
            'items.*.description.required' => __('admin.finance_accounts.description_required'),
            'items.*.qty.required' => __('admin.finance_accounts.quantity_required'),
            'items.*.qty.numeric' => __('admin.finance_accounts.quantity_numeric'),
            'items.*.qty.min' => __('admin.finance_accounts.quantity_min'),
            'items.*.price.required' => __('admin.finance_accounts.price_required'),
            'items.*.price.numeric' => __('admin.finance_accounts.price_numeric'),
            'items.*.price.min' => __('admin.finance_accounts.price_min'),
            'items.*.total_price.required' => __('admin.finance_accounts.total_price_required'),
            'items.*.total_price.numeric' => __('admin.finance_accounts.total_price_numeric'),
            'items.*.total_price.min' => __('admin.finance_accounts.total_price_min'),
            'subtotal.required' => __('admin.finance_accounts.subtotal_required'),
            'subtotal.numeric' => __('admin.finance_accounts.subtotal_numeric'),
            'grand_total.required' => __('admin.finance_accounts.grand_total_required'),
            'grand_total.numeric' => __('admin.finance_accounts.grand_total_numeric'),
        ]);

        try {
            DB::beginTransaction();

            $invoice = Invoice::create([
                'invoice_number' => $request->invoice_number,
                'car_id' => $request->car_id,
                'currency_id' => $request->currency_id,
                'status' => $request->status,
                'biller' => $request->biller,
                'customer_id' => $request->customer_id,
                'payment_method' => $request->payment_method,
                'terms' => $request->terms,
                'notes' => $request->notes,
                'subtotal' => $request->subtotal,
                'tax' => $request->tax ?? 0,
                'grand_total' => $request->grand_total,
                'from_date' => Carbon::parse($request->from_date),
                'to_date' => Carbon::parse($request->to_date),
                'created_at' => Carbon::now(),
            ]);

            foreach ($request->items as $item) {
                $invoice->items()->create([
                    'description' => $item['description'] ?? 0,
                    'qty' => $item['qty'] ?? 0,
                    'price' => $item['price'] ?? 0,
                    'tax' => $item['tax'] ?? 0,
                    'total_price' => $item['total_price'] ?? 0,
                    'created_at' => Carbon::now(),

                ]);
            }

            DB::commit();

            return response()->json(['success' => true,
             'message' => __('admin.finance_accounts.invoice_create_success')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false,
             'message' => __('admin.common.default_create_error.'), 'error' => $e->getMessage()], 500);
        }
    }

    public function edit(?int $id): View
    {
        $invoice = Invoice::findOrFail($id);

        $cars = VehicleInfo::where('status', 1)->where('deleted_at', null)->get();
        $currencies = Currency::where('status', 1)->where('deleted_at', null)->get();
        $users = User::where('status', 1)->where('deleted_at', null)->get();
        $currentUser = Auth::user();
        $payments = GeneralSetting::where('group_id', 13)->where('value', 1)->get();
        $generalSettings = GeneralSetting::where('group_id', 5)->where('key', 'currency')->first();

        $currency = DB::table('currencies')->where('id', ($generalSettings->value ?? ''))->select('symbol')->first();
        $symbol = $currency->symbol ?? '$';

        $bookings = Booking::Join('users', 'bookings.customer_id', '=', 'users.id')
        ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
        ->leftJoin('vehicle_info', 'bookings.vehicle_id', '=', 'vehicle_info.id')
        ->select(
            'bookings.*',
            'users.name as customer',
            'user_details.profile_image',
            'vehicle_info.vehicle_image',
            'vehicle_info.name as vehicle'
        )
        ->whereDate('start_datetime', '>=', Carbon::today())
        ->orderBy('start_datetime', 'asc')->get();


        return view(
            "admin.invoice.edit-invoice",
            compact('cars', 'currencies', 'users', 'currentUser', 'payments', 'symbol', 'invoice', 'bookings')
        );
    }

    public function destroy(?int $id): JsonResponse
    {
        try {
            // Find the invoice by ID
            $invoice = Invoice::findOrFail($id);

            // Delete related items from the items table
            $invoice->items()->delete(); // Assuming you have a relationship set up between Invoices and Items

            // Delete the invoice itself
            $invoice->delete();

            return response()->json(['success' => true,
             'message' => __('admin.finance_accounts.invoice_delete_success')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false,
             'message' => __('admin.common.default_delete_error.'), 'error' => $e->getMessage()], 500);
        }
    }


    public function update(Request $request, ?int $id): RedirectResponse
    {
        //dd($request->all());
        try {
            // Find the invoice to update
            $invoice = Invoice::findOrFail($id);

            // Update the invoice data
            $invoice->update([
                'invoice_number' => $request->invoice_number,
                'car_id' => $request->car_id,
                'from_date' => Carbon::parse($request->from_date),
                'to_date' => Carbon::parse($request->to_date),
                'currency_id' => $request->currency_id,
                'status' => $request->status,
                'payment_method' => $request->payment_method,
                'terms' => $request->terms,
                'notes' => $request->notes,
                'subtotal' => $request->subtotal,
                'tax' => $request->tax ?? 0,
                'grand_total' => $request->grand_total,
                'updated_at' => Carbon::now(),
            ]);

            // First, delete existing invoice items if any
            $invoice->items()->delete();

            // Then, add new invoice items
            $items = json_decode($request->items, true); // decode JSON array

            // Remove existing items before inserting fresh data
            $invoice->items()->delete();

            foreach ($items as $item) {
                Log::info("Creating item: ", $item);

                $invoice->items()->create([
                    'description' => $item['description'] ?? 0,
                    'qty'         => $item['qty'] ?? 0,
                    'price'       => $item['price'] ?? 0,
                    'tax'         => 0,
                    'total_price' => $item['total_price'] ?? 0,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            // Return success response
            return redirect()->route('admin.invoice')
            ->with('success', __('admin.finance_accounts.invoice_update_success'));
        } catch (\Exception $e) {
            // Return error response
            return back()->with('error', __('admin.common.default_update_error'));
        }
    }
}
