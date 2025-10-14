<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface UserProfileRepositoryInterface
{
    public function getProfileSettings(): array;

    public function updateProfile(Request $request): array;

    public function getPreferenceSettings(): array;

    public function updatePreference(Request $request): array;

    public function getPreferences(Request $request): array;

    public function storeEnquiry(Request $request): array;
}
