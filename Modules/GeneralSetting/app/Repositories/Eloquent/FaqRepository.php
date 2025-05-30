<?php

namespace Modules\GeneralSetting\Repositories\Eloquent;

use Modules\GeneralSetting\Models\Faq;
use Modules\GeneralSetting\Models\Language;
use Modules\GeneralSetting\Repositories\Contracts\FaqInterface;

class FaqRepository implements FaqInterface
{
    public function store(array $data)
    {
        $data['order_by'] = (Faq::max('order_by') ?? 0) + 1;
        return Faq::create($data);
    }

    public function update(int $id, array $data)
    {
        $faq = Faq::findOrFail($id);
        $faq->update($data);
        return $faq;
    }

    public function delete(int $id)
    {
        $faq = Faq::findOrFail($id);
        return $faq->delete();
    }

    public function list(array $filters)
    {
        $defaultLanguage = Language::where('default', 1)->value('language_id');

        return Faq::when($filters['language_id'] ?? null, function ($query, $languageId) {
                return $query->where('language_id', $languageId);
            }, function ($query) use ($defaultLanguage) {
                return $query->where('language_id', $defaultLanguage);
            })
            ->when(isset($filters['status']), fn($query) => $query->where('status', $filters['status']))
            ->when($filters['sort_by'] ?? null, function ($query, $sort) {
                return match ($sort) {
                    'asc' => $query->orderBy('order_by', 'asc'),
                    'desc' => $query->orderBy('order_by', 'desc'),
                    'last_7_days' => $query->where('created_at', '>=', now()->subDays(7)),
                    'last_month' => $query->where('created_at', '>=', now()->subMonth()),
                    default => $query->orderBy('order_by', 'desc'),
                };
            })
            ->when($filters['search'] ?? null, function ($query, $search) {
                return $query->where('question', 'like', "%$search%")
                             ->orWhere('answer', 'like', "%$search%");
            })
            ->get();
    }
}
