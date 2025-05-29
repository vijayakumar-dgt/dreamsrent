<?php

namespace Modules\GeneralSetting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SitemapSettingRequest extends FormRequest
{
   public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => ['nullable', 'exists:sitemap_urls,id'],
            'url' => [
                'required',
                'string',
                'max:200',
                $this->id
                    ? 'unique:sitemap_urls,url,' . $this->id . ',id'
                    : 'unique:sitemap_urls,url',
                'regex:/^(https?:\/\/)(localhost|(\d{1,3}\.){3}\d{1,3}|([a-zA-Z0-9.-]+\.[a-zA-Z]{2,}))(:\d+)?(\/.*)?$/'
            ]
        ];
    }

    public function messages()
    {
        return [
            'url.unique' => __('admin.general_settings.url_added'),
        ];
    }
}
