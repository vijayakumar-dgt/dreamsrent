<?php

namespace Modules\Page\Repositories\Eloquent;

use Modules\Page\Repositories\Contracts\SectionInterface;
use Modules\Page\Models\Section;
use Modules\Page\Models\Page;
use Illuminate\Support\Facades\DB;

class SectionRepository implements SectionInterface
{
    public function getAllSections($orderBy, $sortBy, $themeId)
    {
        return Section::orderBy($sortBy, $orderBy)
            ->where("theme_id", $themeId)
            ->where("status", 1)
            ->get();
    }

    public function getFilteredSections($orderBy, $sortBy, $allowedNames)
    {
        return Section::orderBy($sortBy, $orderBy)
            ->where('status', 1)
            ->whereIn('name', $allowedNames)
            ->get();
    }

    public function getSectionData($sectionId, $languageId)
    {
        return DB::table('section_datas')
            ->where('section_id', $sectionId)
            ->where('language_id', $languageId)
            ->value('datas');
    }

    public function updateOrCreateSectionData($sectionId, $languageId, $data)
    {
        $updated = DB::table('section_datas')
            ->where('section_id', $sectionId)
            ->where('language_id', $languageId)
            ->update(['datas' => json_encode($data)]);

        if ($updated === 0) {
            DB::table('section_datas')->insert([
                'section_id' => $sectionId,
                'language_id' => $languageId,
                'datas' => json_encode($data),
            ]);
        }

        return $updated;
    }

    public function updateSectionTitle($sectionId, $title)
    {
        $section = Section::find($sectionId);
        if ($section) {
            $section->title = $title;
            return $section->save();
        }
        return false;
    }

    public function deletePage($id)
    {
        return Page::where('id', $id)->delete();
    }
}
