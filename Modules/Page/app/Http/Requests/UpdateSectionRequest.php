<?php

namespace Modules\Page\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSectionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [];

        if ($this->section_id == 1) {
            $rules = [
                'section_title_one'   => 'sometimes|nullable',
                'description_one'     => 'sometimes|nullable',
                'label_one'           => 'sometimes|nullable',
                'line_one'            => 'sometimes|nullable',
                'line_two'            => 'sometimes|nullable',
                'thumbnail_image_one' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            ];
        } elseif ($this->section_id == 29) {
            $rules = [
                'description_two'     => 'sometimes|nullable',
                'label_two'           => 'sometimes|nullable',
                'thumbnail_image_two' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            ];
        } elseif ($this->section_id == 42) {
            $rules = [
                'vehicle_id' => 'sometimes|nullable',
                'label_1'    => 'sometimes|nullable|max:50',
                'dis_1'      => 'sometimes|nullable|max:100',
                'label_2'    => 'sometimes|nullable|max:50',
                'dis_2'      => 'sometimes|nullable|max:100',
                'label_3'    => 'sometimes|nullable|max:50',
                'dis_3'      => 'sometimes|nullable|max:100',
                'label_4'    => 'sometimes|nullable|max:50',
                'dis_4'      => 'sometimes|nullable|max:100',
                'label_5'    => 'sometimes|nullable|max:50',
                'dis_5'      => 'sometimes|nullable|max:100',
                'label_6'    => 'sometimes|nullable|max:50',
                'dis_6'      => 'sometimes|nullable|max:100',
            ];
        } elseif ($this->section_id == 26) {
            $rules = [
                'why_label_1' => 'sometimes|nullable|max:50',
                'why_dis_1'   => 'sometimes|nullable|max:200',
                'why_label_2' => 'sometimes|nullable|max:50',
                'why_dis_2'   => 'sometimes|nullable|max:200',
                'why_label_3' => 'sometimes|nullable|max:50',
                'why_dis_3'   => 'sometimes|nullable|max:200',
                'why_icon_1'  => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
                'why_icon_2'  => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
                'why_icon_3'  => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'nullable' => __('The :attribute field is nullable.'),
            'max'      => __('The :attribute may not be greater than :max characters.'),
            'image'    => __('The :attribute must be an image.'),
            'mimes'    => __('The :attribute must be a file of type: :values.'),
        ];
    }
}
