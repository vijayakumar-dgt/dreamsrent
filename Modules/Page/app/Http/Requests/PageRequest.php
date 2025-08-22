<?php

namespace Modules\Page\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PageRequest extends FormRequest
{
    /**
     * Validation rule constants to avoid duplication
     * sonarqube(php:S1192) - Define constants instead of duplicating literals
     */
    private const NULLABLE_ARRAY_MIN_1 = 'nullable|array|min:1';
    private const NULLABLE_STRING = 'nullable|string';

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'title'            => 'required|max:100',
            'section_title'    => self::NULLABLE_ARRAY_MIN_1,
            'section_title.*'  => self::NULLABLE_STRING,
            'section_label'    => self::NULLABLE_ARRAY_MIN_1,
            'section_label.*'  => self::NULLABLE_STRING,
            'page_content'     => self::NULLABLE_ARRAY_MIN_1,
            'page_content.*'   => self::NULLABLE_STRING,
            'meta_key'         => self::NULLABLE_STRING,
            'meta_title'       => self::NULLABLE_STRING,
            'meta_description' => self::NULLABLE_STRING,
            'keyword'          => self::NULLABLE_STRING,
            'canonical_url'    => 'nullable|url',
            'og_title'         => self::NULLABLE_STRING,
            'og_description'   => self::NULLABLE_STRING,
            'language_id'      => 'nullable|integer|exists:translation_languages,id',
        ];

        $pageId = $this->input('page_id');

        if (!$pageId) {
            $rules['title'] = 'required|max:100|unique:pages,page_title';
            $rules['slug'] = 'required|max:100|unique:pages,slug';
        } else {
            $rules['page_id'] = 'nullable|exists:pages,id';
            $rules['title'] = 'required|max:100|unique:pages,page_title,' . $pageId;
            $rules['slug'] = 'nullable|max:100|unique:pages,slug,' . $pageId;

            if ($this->input('read') !== 'static') {
                $rules['slug'] = 'nullable|string|max:255|unique:pages,slug,' . $pageId;
            }
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'title.required'           => __('The page title field is required.'),
            'slug.required'            => __('The slug field is required.'),
            'slug.unique'              => __('The slug has already been taken.'),
            'section_title.required'   => __('At least one section title is required.'),
            'section_label.required'   => __('At least one section label is required.'),
            'page_content.required'    => __('At least one page content section is required.'),
            'section_title.*.required' => __('Each section title is required.'),
            'section_label.*.required' => __('Each section label is required.'),
            'page_content.*.required'  => __('Each page content section is required.'),
        ];
    }
}
