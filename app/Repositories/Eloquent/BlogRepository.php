<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BlogRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\GeneralSetting\Models\BlogCategory;
use Modules\GeneralSetting\Models\BlogPost;
use Modules\GeneralSetting\Models\BlogReviews;
use Modules\GeneralSetting\Models\BlogTag;
use Modules\GeneralSetting\Models\Language;
use Modules\GeneralSetting\Models\TranslationLanguage;

class BlogRepository implements BlogRepositoryInterface
{
    public function BlogList(Request $request): View| JsonResponse
    {
        $authUser = current_user();

        $lang_id = null;

        if ($authUser && !empty($authUser->language_id)) {
            $lang_id = $authUser->language_id;
        } elseif (App::getLocale()) {
            $currentLocale = App::getLocale();
            $language = TranslationLanguage::where('code', $currentLocale)->first();
            $lang_id = $language->id ?? null;
        } else {
            $defaultLang = Language::select("language_id")->where("default", 1)->first();
            $lang_id = $defaultLang->language_id ?? 1;
        }

        $languages = Language::with('transLang')->get();

        $query = BlogPost::join('users', 'blog_posts.created_by', '=', 'users.id')
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->leftJoin('blog_categories', 'blog_posts.category', '=', 'blog_categories.id')
            ->leftJoin('blog_tags', 'blog_posts.tags', '=', 'blog_tags.id')
            ->where('blog_posts.language_id', $lang_id)
            ->where('blog_posts.deleted_at', null)
            ->where('blog_posts.status', 1)
            ->select(
                'blog_posts.*',
                'users.name as customer',
                DB::raw('CONCAT(user_details.first_name, " ", user_details.last_name) as full_name'),
                'user_details.profile_image',
                'blog_categories.name as category',
                'blog_tags.name as tag'
            );


        if ($request->has('category') && $request->category !== 'all') {
            $query->where('blog_categories.name', $request->category);
        }


        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('blog_posts.title', 'like', '%' . $request->search . '%')
                    ->orWhere('blog_posts.description', 'like', '%' . $request->search . '%');
            });
        }

        $blogPosts = $query->latest()->paginate(3);

        $categories = BlogCategory::where('deleted_at', null)
            ->where('language_id', $lang_id)->where('status', 1)->get();
        $tags = BlogTag::where('deleted_at', null)->where('language_id', $lang_id)->where('status', 1)->get();

        $latestblogs = BlogPost::latest()->where('blog_posts.language_id', $lang_id)
            ->where('blog_posts.status', 1)->limit(3)->get();

        $seo_title = __('web.blog.blogs_title');

        if ($request->ajax()) {
            return response()->json([
                'html' => view('frontend.blogs.partials.blogs-list', ['blogPosts' => $blogPosts])->render()
            ]);
        }

        return view(
            'frontend.blogs.blog-list',
            ['blogPosts' => $blogPosts, 'languages' => $languages, 'categories' => $categories, 'tags' => $tags, 'latestblogs' => $latestblogs, 'seo_title' => $seo_title]
        );
    }

    public function BlogDetail(int|string $id): array
    {
        $authUser = current_user();

        $lang_id = null;

        if ($authUser && !empty($authUser->language_id)) {
            $lang_id = $authUser->language_id;
        } elseif (App::getLocale()) {
            $currentLocale = App::getLocale();
            $language = TranslationLanguage::where('code', $currentLocale)->first();
            $lang_id = $language->id ?? null;
        } else {
            $defaultLang = Language::select("language_id")->where("default", 1)->first();
            $lang_id = $defaultLang->language_id ?? 1;
        }

        $languages = Language::with('transLang')->get();
        $blogPosts = BlogPost::Join('blog_categories', 'blog_posts.category', '=', 'blog_categories.id')
            ->leftJoin('users', 'blog_posts.created_by', '=', 'users.id')
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->leftJoin('blog_tags', 'blog_posts.tags', '=', 'blog_tags.id')
            ->select(
                'blog_posts.*',
                'blog_categories.name as category',
                'blog_tags.name as tag',
                'users.name as customer',
                DB::raw('CONCAT(user_details.first_name," ",user_details.last_name) as full_name'),
                'user_details.profile_image'
            )
            ->where('blog_posts.slug', $id)
            ->where('blog_posts.status', 1)
            ->first();

        $blogReviews = BlogReviews::where('blog_id', $blogPosts?->id)->latest()->limit(5)->get();
        $countReview = count($blogReviews);

        $otherBlogs = BlogPost::where('slug', '!=', $id)
            ->where('blog_posts.language_id', $lang_id)
            ->where('blog_posts.status', 1)
            ->inRandomOrder()
            ->take(2)
            ->get();
        $seo_title = $blogPosts->title ?? '';

        return ['blogPosts' => $blogPosts, 'languages' => $languages, 'blogReviews' => $blogReviews, 'countReview' => $countReview, 'otherBlogs' => $otherBlogs, 'seo_title' => $seo_title];
    }

    public function storeReview(Request $request): RedirectResponse
    {
        $authUser = Auth::guard('web')->user();
        BlogReviews::create([
            'blog_id'    => $request->blog_id,
            'user_id'    => Auth::id(),
            'name'       => getCurrentUserFullname($authUser->id),
            'email'      => $authUser->email,
            'comments'   => $request->comment,
            'created_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', 'Review Added successfully.');
    }
}
