<?php

namespace App\Repositories\Eloquent;
use Illuminate\Support\Facades\DB;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Models\Invoice;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Models\Currency;
use Illuminate\Http\Request;
use Modules\Booking\Models\Booking;
use Modules\CarInfo\Models\VehicleInfo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Modules\GeneralSetting\Models\Language;


class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function index(): array
    {
       /** @var \App\Models\User|null $authId */
       $authId = current_user();
       $languageId = $authId ? $authId->language_id : null;
       $invoices = Invoice::with('items')
           ->leftJoin('users', 'invoices.customer_id', '=', 'users.id')
           ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
           ->select('invoices.*', 'users.name', 'users.email', 'user_details.profile_image', 'user_details.first_name', 'user_details.last_name')
           ->where('invoices.language_id', $languageId)
           ->where('invoices.deleted_at', null)->orderby('invoices.id', 'desc')
           ->get()->map(function ($invoice) {
               $invoice->full_name = $invoice->first_name ? ($invoice->first_name . ' ' . $invoice->last_name) : '';
               return $invoice;
           });

        $data = ['invoices' => $invoices,];   
        return $data;

    }

    public function addInvoice(): array
    {
        /** @var \App\Models\User|null $authId */
        $authId = current_user();
        $languageId = $authId->language_id ?? null;
        $cars = VehicleInfo::where('status', 1)->where('deleted_at', null)->where('language_id', $languageId)->get();
        $currencies = Currency::where('status', 1)->where('deleted_at', null)->get();
        $users = User::select('users.id', 'users.name', 'user_details.first_name', 'user_details.last_name')
            ->where('users.user_type', 3)
            ->where('users.status', 1)
            ->where('users.deleted_at', null)
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->get()->map(function ($user) {
                $user->full_name = $user->first_name ? ucwords($user->first_name . ' ' . $user->last_name) : '';
                return $user;
            });
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
                'user_details.first_name',
                'user_details.last_name',
                'vehicle_info.vehicle_image',
                'vehicle_info.name as vehicle'
            )
            ->whereDate('start_datetime', '>=', Carbon::today())
            ->orderBy('start_datetime', 'asc')->get()->map(function ($booking) {
                $booking->full_name = $booking->first_name ? ucwords($booking->first_name . ' ' . $booking->last_name) : '';
                return $booking;
            });

        $languages = Language::with('transLang')->where('deleted_at', null)->get();

        $data = ['cars' => $cars, 'currencies' => $currencies, 'users' => $users, 'currentUser' => $currentUser, 'payments' => $payments, 'symbol' => $symbol, 'bookings' => $bookings, 'languages' => $languages];   

        return $data;

    }

    public function store(Request $request)
    {
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
                'language_id' => $request->language_id,
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

            return response()->json([
                'success' => true,
                'code'   => 200,
                'message' => __('admin.finance_accounts.invoice_create_success')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => __('admin.common.default_create_error.'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit(int $id): array
    {
        $invoice = Invoice::findOrFail($id);

        $cars = VehicleInfo::where('status', 1)->where('deleted_at', null)->get();
        $currencies = Currency::where('status', 1)->where('deleted_at', null)->get();
        $users = User::select('users.id', 'users.name', 'user_details.first_name', 'user_details.last_name')
            ->where('users.user_type', 3)
            ->where('users.status', 1)
            ->where('users.deleted_at', null)
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->get()->map(function ($user) {
                $user->full_name = $user->first_name ? ucwords($user->first_name . ' ' . $user->last_name) : '';
                return $user;
            });
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
                'user_details.first_name',
                'user_details.last_name',
                'vehicle_info.vehicle_image',
                'vehicle_info.name as vehicle'
            )
            ->whereDate('start_datetime', '>=', Carbon::today())
            ->orderBy('start_datetime', 'asc')->get()->map(function ($booking) {
                $booking->full_name = $booking->first_name ? ucwords($booking->first_name . ' ' . $booking->last_name) : '';
                return $booking;
            });

        $languages = Language::with('transLang')->where('deleted_at', null)->get();

        $data = ['cars' => $cars, 'currencies' => $currencies, 'users' => $users, 'currentUser' => $currentUser, 'payments' => $payments, 'symbol' => $symbol, 'invoice' => $invoice, 'bookings' => $bookings, 'languages' => $languages];   

        return $data;

    }

    public function delete(int $id)
    {
        try {
            $invoice = Invoice::findOrFail($id);
            $invoice->items()->delete();
            $invoice->delete();

            return response()->json([
                'success' => true,
                'message' => __('admin.finance_accounts.invoice_delete_success')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('admin.common.default_delete_error.'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, ?int $id): RedirectResponse
    {
        try {
            $invoice = Invoice::findOrFail($id);
            $invoice->update([
                'invoice_number' => $request->invoice_number,
                'car_id' => $request->car_id,
                'from_date' => Carbon::parse($request->from_date),
                'to_date' => Carbon::parse($request->to_date),
                'currency_id' => $request->currency_id,
                'status' => $request->status,
                'payment_method' => $request->payment_method,
                'language_id' => $request->language_id,
                'terms' => $request->terms,
                'notes' => $request->notes,
                'subtotal' => $request->subtotal,
                'tax' => $request->tax ?? 0,
                'grand_total' => $request->grand_total,
                'updated_at' => Carbon::now(),
            ]);

            $invoice->items()->delete();
            $items = json_decode($request->items, true);
            $invoice->items()->delete();

            foreach ($items as $item) {
                $invoice->items()->create([
                    'description' => $item['description'] ?? 0,
                    'qty' => $item['qty'] ?? 0,
                    'price' => $item['price'] ?? 0,
                    'tax' => 0,
                    'total_price' => $item['total_price'] ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return redirect()->route('admin.invoice')
                ->with('success', __('admin.finance_accounts.invoice_update_success'));
        } catch (\Exception $e) {
            return back()->with('error', __('admin.common.default_update_error'));
        }
    }

}