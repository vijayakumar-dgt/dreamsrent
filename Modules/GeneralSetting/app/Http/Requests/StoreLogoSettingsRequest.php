<?php

namespace Modules\GeneralSetting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLogoSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'logo_image'    => 'nullable|mimes:jpg,jpeg,png,svg|max:5120',
            'favicon_image' => 'nullable|image|mimes:jpg,jpeg,png,svg,ico|max:5120',
            'small_image'   => 'nullable|image|mimes:jpg,jpeg,png,svg|max:5120',
            'dark_logo'     => 'nullable|mimes:jpg,jpeg,png,svg|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'logo_image.image'    => __('admin.general_settings.logo_image_type'),
            'favicon_image.image' => __('admin.general_settings.favicon_image_type'),
            'small_image.image'   => __('admin.general_settings.small_image_type'),
            'dark_logo.image'     => __('admin.general_settings.dark_logo_image_type'),
            'logo_image.max'      => __('admin.general_settings.logo_image_size'),
            'favicon_image.max'   => __('admin.general_settings.favicon_image_size'),
            'small_image.max'     => __('admin.general_settings.small_image_size'),
            'dark_logo.max'       => __('admin.general_settings.dark_logo_image_size'),
        ];
    }
}
