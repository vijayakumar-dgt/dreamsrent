<?php

namespace Modules\GeneralSetting\Repositories\Contracts;

interface LanguageSettingInterface
{
    public function index();
    
    public function addLanguage(array $data): array;
    
    public function getLanguages(array $filters = []): array;
    
    public function updateLanguageSettings(int $id, array $data): array;
    
    public function changeLanguage(string $languageCode): array;
    
    public function userFlagChangeLanguage(string $languageCode): array;
    
    public function getLanguageModules(string $code, string $tab, ?string $search = null): array;
    
    public function editModuleLanguage(string $code, string $tab, string $module, ?string $keyword = null): array;
    
    public function updateModuleLanguage(string $code, string $tab, string $module, string $key, string $value): array;
    
    public function deleteLanguage(int $id): array;
    
    public function languageDetails(string $code, string $type): array;
}