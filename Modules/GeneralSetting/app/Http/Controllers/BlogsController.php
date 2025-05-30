<?php

namespace Modules\GeneralSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\GeneralSetting\Models\BlogCategory;
use Modules\GeneralSetting\Repositories\Contracts\BlogCategoryRepositoryInterface;
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
use Modules\GeneralSetting\Http\Requests\BlogCategoryRequest;

class BlogsController extends Controller
{
    
    protected BlogCategoryRepositoryInterface $blogRepository;

    public function __construct(BlogCategoryRepositoryInterface $blogRepository)
    {
        $this->blogRepository = $blogRepository;
    }
    public function blogCategory(): View
    {
        $data = $this->blogRepository->blogCategory();
        return view('generalsetting::cms.blogs.blog-category', [...$data]);
    }

    public function categoryStore(BlogCategoryRequest $request): JsonResponse
    {
        $response = $this->blogRepository->categoryStore($request);
        return $response;
        
    }
    public function categoryUpdate(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name,' . $id,
            'status' => 'boolean',
        ]);
        $category = BlogCategory::where('id', $id)->firstOrFail();
        $category->update([
            'name' => $request->name,
            'status' => $request->status ?? 0,
        ]);
        return redirect()->back()->with('success', 'Blog Category updated successfully.');
    }
    public function categoryDestroy(int $id): RedirectResponse
    {
        $category = BlogCategory::where('id', $id)->firstOrFail();
        $category->delete();
        return redirect()->back()->with('success', 'Blog Category deleted successfully.');
    }
    public function blogTags(Request $request): View
    {
        /** @var \App\Models\User|null $authId */
        $authId = current_user();
        $languageId = $authId ? $authId->language_id : null;
        $languages = Language::with('transLang')->get();
        $tags = BlogTag::where('deleted_at', null)->where('language_id', $languageId)->orderBy('name', 'asc')->get();
        return view('generalsetting::cms.blogs.blog-tags', compact('languages', 'tags'));
    }
    public function tagStore(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:blog_tags,name',
            'language_id' => 'required',
        ]);
        BlogTag::create([
            'name' => $request->name,
            'status' => 1,
            'created_at' =>  Carbon::now(),
            'language_id' => $request->language_id,
        ]);
        return response()->json([
            'code' => 200,
            'message' => 'Blog Tag added successfully!'
        ], 200);
    }
    /**
     * Update the tag with the given ID.
     *
     * @param \Illuminate\Http\Request $request The HTTP request instance.
     * @param int $id The ID of the tag to update.
     * @return \Illuminate\Http\RedirectResponse Redirect response after updating the tag.
     */
    public function tagUpdate(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:blog_tags,name,' . $id,
            'status' => 'boolean',
        ]);
        $tag = BlogTag::findOrFail($id);
        $tag->update([
            'name' => $request->name,
            'status' => $request->status ?? 0,
        ]);
        return redirect()->back()->with('success', 'Blog Tag updated successfully.');
    }
    public function tagDestroy(int $id): RedirectResponse
    {
        $tag = BlogTag::where('id', $id)->firstOrFail();
        $tag->delete();
        return redirect()->back()->with('success', 'Blog Tag deleted successfully.');
    }
    public function blogComments(Request $request): View
    {
        $comments = BlogReviews::Join('blog_posts', 'blog_reviews.blog_id', '=', 'blog_posts.id')->select('blog_reviews.*', 'blog_posts.title')->where('blog_reviews.deleted_at', null)->get();
        return view('generalsetting::cms.blogs.blog-comments', compact('comments'));
    }
    public function blogs(Request $request): View
    {
        /** @var \App\Models\User|null $authId */
        $authId = current_user();
        $languageId = $authId ? $authId->language_id : null;
        $languages = Language::with('transLang')->get();
        $blogPosts = BlogPost::Join('users', 'blog_posts.created_by', '=', 'users.id')
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->where('blog_posts.language_id', $languageId)
            ->where('blog_posts.deleted_at', null)
            ->select(
                'blog_posts.*',
                'users.name',
                DB::raw('CONCAT(user_details.first_name," ",user_details.last_name) as full_name'),
                'user_details.profile_image'
            )
            ->orderBy('blog_posts.id', 'desc')
            ->get();
        $categories = BlogCategory::where('deleted_at', null)->where('language_id', $languageId)->get();
        $tags = BlogTag::where('deleted_at', null)->where('language_id', $languageId)->get();
        return view('generalsetting::cms.blogs.blogs', compact('blogPosts', 'languages', 'categories', 'tags'));
    }
    public function blogDetails(string $id): View
    {
        $languages = Language::with('transLang')->get();
        $blogPosts = BlogPost::Join('blog_categories', 'blog_posts.category', '=', 'blog_categories.id')
            ->leftJoin('blog_tags', 'blog_posts.tags', '=', 'blog_tags.id')
            ->select('blog_posts.*', 'blog_categories.name as category', 'blog_tags.name as tag')
            ->where('blog_posts.slug', $id)
            ->first();
        return view('generalsetting::cms.blogs.blog-details', compact('blogPosts', 'languages'));
    }
    public function blogAdd(Request $request): View
    {
        /** @var \App\Models\User|null $authId */
        $authId = current_user();
        $languageId = $authId ? $authId->language_id : null;
        $languages = Language::with('transLang')->where('deleted_at', null)->get();
        $tags = BlogTag::where('deleted_at', null)->where('language_id', $languageId)->where('status', '1')->get();
        $categories = BlogCategory::where('deleted_at', null)->where('language_id', $languageId)->where('status', '1')->get();
        return view('generalsetting::cms.blogs.add-blog', compact('languages', 'tags', 'categories'));
    }
    public function blogStore(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'language' => 'required',
            'category_id' => 'required',
            'tag_id' => 'required|array',
            'description' => 'nullable',
            'image' => 'required|image|max:5120', // 5MB
        ]);
        assert($request->file('image') instanceof \Illuminate\Http\UploadedFile);
        $imagePath = $request->hasFile('image') ? $request->file('image')->store('blogs/images', 'public') : null;
        BlogPost::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'language_id' => $request->language,
            'category' => $request->category_id,
            'tags' => json_encode($request->tag_id),
            'description' => $request->description,
            'image' => $imagePath,
            'status' => 1,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
            'created_at' => Carbon::now(),
        ]);
        return response()->json(['message' => 'Blog added successfully!']);
    }
    public function blogDestroy(int $id): JsonResponse
    {
        $blog = BlogPost::findOrFail($id);
        $blog->deleted_at = \Illuminate\Support\Carbon::now();
        $blog->save();
        return response()->json(['success' => true, 'message' => 'Blog deleted successfully']);
    }
    public function blogEdit(int $id): View
    {
        /** @var \App\Models\User|null $authId */
        $authId = current_user();
        $languageId = $authId ? $authId->language_id : null;
        $blog = BlogPost::findOrFail($id);
        $languages = Language::with('transLang')->where('deleted_at', null)->get();
        $tags = BlogTag::where('deleted_at', null)->where('language_id', $languageId)->where('status', '1')->get();
        $categories = BlogCategory::where('deleted_at', null)->where('language_id', $languageId)->where('status', '1')->get();
        return view('generalsetting::cms.blogs.edit-blog', compact('blog', 'languages', 'tags', 'categories'));
    }
    public function BlogUpdate(Request $request, int $id): JsonResponse
    {
        $blog = BlogPost::where('id', $id)->firstOrFail();
        $blog->title = $request->input('title');
        $blog->slug = Str::slug($request->input('title'));
        $blog->category = $request->input('category_id');
        $blog->tags = json_encode($request->input('tag_id')) ?: '[]';
        $blog->status = ($request->input('status') ? 1 : 0);
        $blog->description = $request->input('description');
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            if ($file) {
                $path = $file->store('blogs/images', 'public');
                if (is_string($path)) {
                    $blog->image = $path;
                }
            }
        }
        $blog->save();
        return response()->json(['success' => true]);
    }
}
