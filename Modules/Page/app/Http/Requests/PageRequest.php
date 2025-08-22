<?php

namespace Modules\Page\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'title'            => 'required|max:100',
            'section_title'    => 'nullable|array|min:1',
            'section_title.*'  => 'nullable|string',
            'section_label'    => 'nullable|array|min:1',
            'section_label.*'  => 'nullable|string',
            'page_content'     => 'nullable|array|min:1',
            'page_content.*'   => 'nullable|string',
            'meta_key'         => 'nullable|string',
            'meta_title'       => 'nullable|string',
            'meta_description' => 'nullable|string',
            'keyword'          => 'nullable|string',
            'canonical_url'    => 'nullable|url',
            'og_title'         => 'nullable|string',
            'og_description'   => 'nullable|string',
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
