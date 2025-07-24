<?php

namespace Modules\GeneralSetting\Http\Requests;

use App\Library\CustomFailedValidation;

class StoreInvoiceSettingsRequest extends CustomFailedValidation
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_logo'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'invoice_prefix'       => 'required|string|max:10',
            'invoice_due'          => 'required|integer|min:1',
            'invoice_round_off'    => 'nullable|numeric',
            'round_off_enabled'    => 'nullable|in:on,off',
            'show_company_details' => 'nullable|in:on,off',
            'invoice_terms'        => 'nullable|string',
        ];
    }
}
