<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface UserProfileRepositoryInterface
{
    public function getProfileSettings();

    public function updateProfile(Request $request);

    public function getPreferenceSettings();

    public function updatePreference(Request $request);

    public function getPreferences(Request $request);

    public function storeEnquiry(Request $request);
}
