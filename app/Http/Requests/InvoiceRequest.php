<?php

namespace App\Http\Requests;

use App\Library\CustomFailedValidation;

class InvoiceRequest extends CustomFailedValidation
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'car_id'              => 'required',
            'currency_id'         => 'required',
            'status'              => 'required|string',
            'biller'              => 'required|string',
            'customer_id'         => 'required',
            'payment_method'      => 'required|string',
            'terms'               => 'required|string',
            'notes'               => 'required|string',
            'items'               => 'required',
            'items.*.description' => 'required|string',
            'items.*.qty'         => 'required|numeric|min:1',
            'items.*.price'       => 'required|numeric|min:0',
            'items.*.total_price' => 'required|numeric|min:0',
            'subtotal'            => 'required|numeric',
            'grand_total'         => 'required|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'car_id.required'              => __('admin.finance_accounts.vehicle_required'),
            'currency_id.required'         => __('admin.finance_accounts.currency_required'),
            'status.required'              => __('admin.finance_accounts.status_required'),
            'biller.required'              => __('admin.finance_accounts.biller_required'),
            'customer_id.required'         => __('admin.finance_accounts.customer_required'),
            'payment_method.required'      => __('admin.finance_accounts.payment_method_required'),
            'terms.required'               => __('admin.finance_accounts.terms_condition_required'),
            'notes.required'               => __('admin.finance_accounts.notes_required'),
            'items.required'               => __('admin.finance_accounts.items_required'),
            'items.*.description.required' => __('admin.finance_accounts.description_required'),
            'items.*.qty.required'         => __('admin.finance_accounts.quantity_required'),
            'items.*.qty.numeric'          => __('admin.finance_accounts.quantity_numeric'),
            'items.*.qty.min'              => __('admin.finance_accounts.quantity_min'),
            'items.*.price.required'       => __('admin.finance_accounts.price_required'),
            'items.*.price.numeric'        => __('admin.finance_accounts.price_numeric'),
            'items.*.price.min'            => __('admin.finance_accounts.price_min'),
            'items.*.total_price.required' => __('admin.finance_accounts.total_price_required'),
            'items.*.total_price.numeric'  => __('admin.finance_accounts.total_price_numeric'),
            'items.*.total_price.min'      => __('admin.finance_accounts.total_price_min'),
            'subtotal.required'            => __('admin.finance_accounts.subtotal_required'),
            'subtotal.numeric'             => __('admin.finance_accounts.subtotal_numeric'),
            'grand_total.required'         => __('admin.finance_accounts.grand_total_required'),
            'grand_total.numeric'          => __('admin.finance_accounts.grand_total_numeric'),
        ];
    }
}
