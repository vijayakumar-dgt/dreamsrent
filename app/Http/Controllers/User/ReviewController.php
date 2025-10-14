<?php

namespace App\Http\Controllers\User;

use Illuminate\View\View;

class ReviewController extends BaseUserController
{
    public function index(): View
    {
        $seoTitle = __('web.common.reviews');

        return view('frontend.user.reviews', ['seo_title' => $seoTitle]);
    }
}
