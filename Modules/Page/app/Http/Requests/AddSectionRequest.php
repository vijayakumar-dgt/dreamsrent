<?php

namespace Modules\Page\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddSectionRequest extends FormRequest
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
                'section_title_one' => 'required',
                'description_one' => 'required',
                'label_one' => 'required',
                'line_one' => 'required',
                'line_two' => 'required',
                'thumbnail_image_one' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            ];
        } elseif ($this->section_id == 29) {
            $rules = [
                'description_two' => 'nullable',
                'label_two' => 'nullable',
                'thumbnail_image_two' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            ];
        } elseif ($this->section_id == 43) {
            $rules = [
                'description_three' => 'required',
                'label_three_three' => 'required',
                'label_three_two' => 'required',
                'label_three_one' => 'required',
                'thumbnail_image_four' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            ];
        } elseif ($this->section_id == 56) {
            $rules = [
                'description_boat' => 'required',
                'label_boat_three' => 'required',
                'label_boat_two' => 'required',
                'label_boat_one' => 'required',
            ];
        } elseif ($this->section_id == 42) {
            $rules = [
                'vehicle_id' => 'nullable',
                'label_1' => 'required|max:50',
                'dis_1' => 'required|max:100',
                'label_2' => 'required|max:50',
                'dis_2' => 'required|max:100',
                'label_3' => 'required|max:50',
                'dis_3' => 'required|max:100',
                'label_4' => 'required|max:50',
                'dis_4' => 'required|max:100',
                'label_5' => 'required|max:50',
                'dis_5' => 'required|max:100',
                'label_6' => 'required|max:50',
                'dis_6' => 'required|max:100',
            ];
        } elseif ($this->section_id == 26) {
            $rules = [
                'why_label_1' => 'required|max:50',
                'why_dis_1' => 'required|max:200',
                'why_label_2' => 'required|max:50',
                'why_dis_2' => 'required|max:200',
                'why_label_3' => 'required|max:50',
                'why_dis_3' => 'required|max:200',
                'why_icon_1' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
                'why_icon_2' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
                'why_icon_3' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'nullable' => __('The :attribute field is nullable.'),
            'max' => __('The :attribute may not be greater than :max characters.'),
            'image' => __('The :attribute must be an image.'),
            'mimes' => __('The :attribute must be a file of type: :values.'),
        ];
    }
}
