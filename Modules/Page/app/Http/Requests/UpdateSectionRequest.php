<?php

namespace Modules\Page\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSectionRequest extends FormRequest
{
    /**
     * Validation rule constants to avoid duplication
     * sonarqube(php:S1192) - Define constants instead of duplicating literals
     */
    private const IMAGE_VALIDATION_RULE = 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048';
    private const SOMETIMES_NULLABLE = 'sometimes|nullable';
    private const SOMETIMES_NULLABLE_MAX_50 = 'sometimes|nullable|max:50';
    private const SOMETIMES_NULLABLE_MAX_100 = 'sometimes|nullable|max:100';
    private const SOMETIMES_NULLABLE_MAX_200 = 'sometimes|nullable|max:200';

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [];

        if ($this->section_id == 1) {
            $rules = [
                'section_title_one'   => self::SOMETIMES_NULLABLE,
                'description_one'     => self::SOMETIMES_NULLABLE,
                'label_one'           => self::SOMETIMES_NULLABLE,
                'line_one'            => self::SOMETIMES_NULLABLE,
                'line_two'            => self::SOMETIMES_NULLABLE,
                'thumbnail_image_one' => self::IMAGE_VALIDATION_RULE,
            ];
        } elseif ($this->section_id == 29) {
            $rules = [
                'description_two'     => self::SOMETIMES_NULLABLE,
                'label_two'           => self::SOMETIMES_NULLABLE,
                'thumbnail_image_two' => self::IMAGE_VALIDATION_RULE,
            ];
        } elseif ($this->section_id == 42) {
            $rules = [
                'vehicle_id' => self::SOMETIMES_NULLABLE,
                'label_1'    => self::SOMETIMES_NULLABLE_MAX_50,
                'dis_1'      => self::SOMETIMES_NULLABLE_MAX_100,
                'label_2'    => self::SOMETIMES_NULLABLE_MAX_50,
                'dis_2'      => self::SOMETIMES_NULLABLE_MAX_100,
                'label_3'    => self::SOMETIMES_NULLABLE_MAX_50,
                'dis_3'      => self::SOMETIMES_NULLABLE_MAX_100,
                'label_4'    => self::SOMETIMES_NULLABLE_MAX_50,
                'dis_4'      => self::SOMETIMES_NULLABLE_MAX_100,
                'label_5'    => self::SOMETIMES_NULLABLE_MAX_50,
                'dis_5'      => self::SOMETIMES_NULLABLE_MAX_100,
                'label_6'    => self::SOMETIMES_NULLABLE_MAX_50,
                'dis_6'      => self::SOMETIMES_NULLABLE_MAX_100,
            ];
        } elseif ($this->section_id == 26) {
            $rules = [
                'why_label_1' => self::SOMETIMES_NULLABLE_MAX_50,
                'why_dis_1'   => self::SOMETIMES_NULLABLE_MAX_200,
                'why_label_2' => self::SOMETIMES_NULLABLE_MAX_50,
                'why_dis_2'   => self::SOMETIMES_NULLABLE_MAX_200,
                'why_label_3' => self::SOMETIMES_NULLABLE_MAX_50,
                'why_dis_3'   => self::SOMETIMES_NULLABLE_MAX_200,
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
