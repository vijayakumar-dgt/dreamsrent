<?php

namespace Modules\Communication\Http\Requests;

use App\Library\CustomFailedValidation;

class AnnouncementRequest extends CustomFailedValidation
{
    public function rules(): array
    {
        return [
            'announcement_title' => 'required|string|max:100',
            'user_type' => 'required|in:user,admin',
            'description' => 'required|string|max:500',
            'announcement_type' => 'nullable|in:general,important,urgent',
            'status' => 'nullable|in:0,1',
            'id' => 'nullable|exists:announcements,id',
        ];
    }

    public function messages(): array
    {
        return [
            'announcement_title.required' => __('admin.support.title_required'),
            'user_type.required' => __('admin.support.user_type_required'),
            'user_type.in' => __('admin.support.invalid_user_type'),
            'description.required' => __('admin.support.description_required'),
            'announcement_type.in' => __('admin.support.invalid_announcement_type'),
            'status.in' => __('admin.support.invalid_status'),
            'id.exists' => __('admin.support.announcement_not_found'),
        ];
    }
}
