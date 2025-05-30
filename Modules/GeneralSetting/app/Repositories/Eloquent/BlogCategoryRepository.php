<?php
namespace Modules\GeneralSetting\Repositories\Eloquent;
use Modules\GeneralSetting\Models\BlogCategory;
use Modules\GeneralSetting\Repositories\Contracts\BlogCategoryRepositoryInterface;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\GeneralSetting\Models\BlogReviews;
use Modules\GeneralSetting\Models\BlogTag;
use Modules\GeneralSetting\Models\BlogPost;
use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Modules\GeneralSetting\Models\Language;
use Illuminate\Support\Str;

class BlogCategoryRepository implements BlogCategoryRepositoryInterface
{
    public function blogCategory(): array
    {
       /** @var \App\Models\User|null $authId */
       $authId = current_user();
       $languageId = $authId ? $authId->language_id : null;
       $languages = Language::with('transLang')->get();
       $categories = BlogCategory::where('deleted_at', null)->where('language_id', $languageId)->orderBy('name', 'asc')->get();

       $data = ['languages' => $languages, 'categories' => $categories];   

        return $data;
    }

    public function categoryStore(Request $request): JsonResponse
    {
        BlogCategory::create([
            'name' => $request->name,
            'status' => 1,
            'created_at' =>  Carbon::now(),
            'language_id' => $request->language_id,
        ]);
        return response()->json([
            'code' => 200,
            'message' => 'Blog Category added successfully!'
        ], 200);
    }
}