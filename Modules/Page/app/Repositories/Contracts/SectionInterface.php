<?php

namespace Modules\Page\Repositories\Contracts;

interface SectionInterface
{
    public function getAllSections($orderBy, $sortBy, $themeId);
    public function getFilteredSections($orderBy, $sortBy, $allowedNames);
    public function getSectionData($sectionId, $languageId);
    public function updateOrCreateSectionData($sectionId, $languageId, $data);
    public function updateSectionTitle($sectionId, $title);
    public function deletePage($id);
}