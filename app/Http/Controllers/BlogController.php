<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\GeneralSetting\Models\BlogCategory;
use Modules\GeneralSetting\Models\BlogTag;
use Modules\GeneralSetting\Models\Language;
use Modules\GeneralSetting\Models\BlogPost;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Modules\GeneralSetting\Models\TranslationLanguage;
use Illuminate\Support\Facades\App;
use Modules\GeneralSetting\Models\BlogReviews;

class BlogController extends Controller
{
    public function BlogList(Request $request)
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

        $latestblogs =  BlogPost::latest()->where('blog_posts.language_id', $lang_id)
        ->where('blog_posts.status', 1)->limit(3)->get();

        $seo_title  = __('web.blog.blogs_title');

        // If request is AJAX, return only the blog list HTML
        if ($request->ajax()) {
            return response()->json([
                'html' => view('frontend.blogs.partials.blogs-list', compact('blogPosts'))->render()
            ]);
        }

        return view(
            'frontend.blogs.blog-list',
            compact('blogPosts', 'languages', 'categories', 'tags', 'latestblogs', 'seo_title')
        );
    }


    public function BlogGrid()
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
        $blogPosts = BlogPost::Join('users', 'blog_posts.created_by', '=', 'users.id')
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->leftJoin('blog_categories', 'blog_posts.category', '=', 'blog_categories.id')
            ->leftJoin('blog_tags', 'blog_posts.tags', '=', 'blog_tags.id')
            ->where('blog_posts.language_id', $lang_id)
            ->where('blog_posts.deleted_at', null)
            ->where('blog_posts.status', 1)
            ->select(
                'blog_posts.*',
                'users.name as customer',
                'user_details.profile_image',
                'blog_categories.name as category',
                'blog_tags.name as tag'
            )
            ->paginate(4);

        $categories = BlogCategory::where('deleted_at', null)
        ->where('language_id', $lang_id)->where('status', 1)->get();
        $tags = BlogTag::where('deleted_at', null)->where('language_id', $lang_id)->where('status', 1)->get();

        $latestblogs =  BlogPost::latest()
        ->where('blog_posts.language_id', $lang_id)->where('blog_posts.status', 1)->limit(3)->get();

        $seo_title  = __('web.blog.blogs_title');
        return view('frontend.blogs.blog-grid', compact(
            'blogPosts',
            'languages',
            'categories',
            'tags',
            'latestblogs',
            'seo_title'
        ));
    }

    public function BlogDetail($id)
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
                'user_details.profile_image'
            )
            ->where('blog_posts.slug', $id)
            ->where('blog_posts.status', 1)
            ->first();

        $blogReviews = BlogReviews::where('blog_id', $blogPosts->id)->latest()->limit(5)->get();
        $countReview = count($blogReviews);

        $otherBlogs = BlogPost::where('slug', '!=', $id)
            ->where('blog_posts.language_id', $lang_id)
            ->where('blog_posts.status', 1)
            ->inRandomOrder()
            ->take(2)
            ->get();
        $seo_title  = $blogPosts->title;
        return view(
            'frontend.blogs.blog-details',
            compact('blogPosts', 'languages', 'blogReviews', 'countReview', 'otherBlogs', 'seo_title')
        );
    }

    public function storeReview(Request $request)
    {
        $request->validate([
            'blog_id' => 'required',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'comment' => 'required|string|max:1000',
        ]);

        BlogReviews::create([
            'blog_id' => $request->blog_id,
            'user_id' => Auth::id(),
            'name' => $request->name,
            'email' => $request->email,
            'comments' => $request->comment,
            'created_at' =>  Carbon::now(),
        ]);

        return redirect()->back()->with('success', 'Review Added successfully.');
    }
}
