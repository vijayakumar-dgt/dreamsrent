<?php

namespace Modules\GeneralSetting\Http\Requests;

use App\Library\CustomFailedValidation;

class FaqUpdateRequest extends CustomFailedValidation
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:faqs,id',
            'question' => 'required|string|max:255|unique:faqs,question,' . $this->id,
            'answer' => 'required|string',
            'language' => 'required|integer|exists:translation_languages,id',
            'status' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => __('admin.cms.faq_id_required'),
            'id.exists' => __('admin.cms.faq_id_invalid'),
            'question.required' => __('admin.cms.question_required'),
            'question.unique' => __('admin.cms.question_unique'),
            'answer.required' => __('admin.cms.answer_required'),
            'language.required' => __('admin.cms.language_required'),
            'language.exists' => __('admin.cms.language_exists'),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
