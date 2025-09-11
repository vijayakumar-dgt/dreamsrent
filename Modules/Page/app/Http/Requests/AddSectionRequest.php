<?php

namespace Modules\Page\Http\Requests;

use App\Library\CustomFailedValidation;

class AddSectionRequest extends CustomFailedValidation
{
    /**
     * Validation rule constants to avoid duplication
     * sonarqube(php:S1192) - Define constants instead of duplicating literals
     */
    private const IMAGE_VALIDATION_RULE = 'sometimes|mimes:jpeg,png,jpg,gif,svg|max:2048';
    private const REQUIRED_MAX_50 = 'required|max:50';
    private const REQUIRED_MAX_100 = 'required|max:100';
    private const REQUIRED_MAX_200 = 'required|max:200';

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [];

        if ($this->section_id == 1) {
            $rules = [
                'section_title_one'   => 'required',
                'description_one'     => 'required',
                'label_one'           => 'required',
                'line_one'            => 'required',
                'line_two'            => 'required',
                'thumbnail_image_one' => self::IMAGE_VALIDATION_RULE,
            ];
        } elseif ($this->section_id == 29) {
            $rules = [
                'description_two'     => 'nullable',
                'label_two'           => 'nullable',
                'thumbnail_image_two' => self::IMAGE_VALIDATION_RULE,
            ];
        } elseif ($this->section_id == 43) {
            $rules = [
                'description_three'    => 'required',
                'label_three_three'    => 'required',
                'label_three_two'      => 'required',
                'label_three_one'      => 'required',
                'thumbnail_image_four' => self::IMAGE_VALIDATION_RULE,
            ];
        } elseif ($this->section_id == 56) {
            $rules = [
                'description_boat' => 'required',
                'label_boat_three' => 'required',
                'label_boat_two'   => 'required',
                'label_boat_one'   => 'required',
            ];
        } elseif ($this->section_id == 42) {
            $rules = [
                'vehicle_id' => 'nullable',
                'label_1'    => self::REQUIRED_MAX_50,
                'dis_1'      => self::REQUIRED_MAX_100,
                'label_2'    => self::REQUIRED_MAX_50,
                'dis_2'      => self::REQUIRED_MAX_100,
                'label_3'    => self::REQUIRED_MAX_50,
                'dis_3'      => self::REQUIRED_MAX_100,
                'label_4'    => self::REQUIRED_MAX_50,
                'dis_4'      => self::REQUIRED_MAX_100,
                'label_5'    => self::REQUIRED_MAX_50,
                'dis_5'      => self::REQUIRED_MAX_100,
                'label_6'    => self::REQUIRED_MAX_50,
                'dis_6'      => self::REQUIRED_MAX_100,
            ];
        } elseif ($this->section_id == 26) {
            $rules = [
                'why_label_1' => self::REQUIRED_MAX_50,
                'why_dis_1'   => self::REQUIRED_MAX_200,
                'why_label_2' => self::REQUIRED_MAX_50,
                'why_dis_2'   => self::REQUIRED_MAX_200,
                'why_label_3' => self::REQUIRED_MAX_50,
                'why_dis_3'   => self::REQUIRED_MAX_200,
                'why_icon_1'  => self::IMAGE_VALIDATION_RULE,
                'why_icon_2'  => self::IMAGE_VALIDATION_RULE,
                'why_icon_3'  => self::IMAGE_VALIDATION_RULE,
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
