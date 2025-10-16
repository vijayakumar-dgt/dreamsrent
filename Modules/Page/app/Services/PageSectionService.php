<?php

namespace Modules\Page\Services;

use Illuminate\Http\Request;

class PageSectionService
{
    public function prepareSections(Request $request): array
    {
        $sections = [];
        $titles = $request->input('section_title', []);
        $labels = $request->input('section_label', []);
        $contents = $request->input('page_content', []);
        $statuses = $request->input('page_status', []);

        for ($i = 0; $i < count($titles); $i++) {
            $sections[] = [
                'section_title'   => $titles[$i] ?? '',
                'section_label'   => $labels[$i] ?? '',
                'section_content' => $contents[$i] ?? '',
                'status'          => isset($statuses[$i]) ? 1 : 0,
            ];
        }

        return $sections;
    }
}
