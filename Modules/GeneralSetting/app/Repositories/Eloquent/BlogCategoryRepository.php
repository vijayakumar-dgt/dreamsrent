<?php

namespace Modules\GeneralSetting\Repositories\Eloquent;

use App\Services\ImageResizer;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\GeneralSetting\Models\BlogCategory;
use Modules\GeneralSetting\Models\BlogPost;
use Modules\GeneralSetting\Models\BlogReviews;
use Modules\GeneralSetting\Models\BlogTag;
use Modules\GeneralSetting\Models\Language;
use Modules\GeneralSetting\Repositories\Contracts\BlogCategoryRepositoryInterface;

class BlogCategoryRepository implements BlogCategoryRepositoryInterface
{
    protected ImageResizer $imageResizer;

    public function __construct(ImageResizer $imageResizer)
    {
        $this->imageResizer = $imageResizer;
    }

    public function blogCategory(): array
    {
        /** @var \App\Models\User|null $authId */
        $authId = current_user();
        $languageId = $authId ? $authId->language_id : null;
        $languages = Language::with('transLang')->get();
        $categories = BlogCategory::where('deleted_at', null)
            ->where('language_id', $languageId)
            ->orderBy('name', 'asc')
            ->get();

        return [
            'languages'  => $languages,
            'categories' => $categories,
        ];
    }

    public function categoryStore(Request $request): JsonResponse
    {
        BlogCategory::create([
            'name'        => $request->name,
            'status'      => 1,
            'created_at'  => Carbon::now(),
            'language_id' => $request->language_id,
        ]);
        return response()->json([
            'code'    => 200,
            'message' => 'Blog Category added successfully!'
        ], 200);
    }

    public function categoryUpdate(Request $request, int $id): RedirectResponse
    {
        $category = BlogCategory::where('id', $id)->firstOrFail();
        $category->update([
            'name'   => $request->name,
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

    public function blogTags(): array
    {
        /** @var \App\Models\User|null $authId */
        $authId = current_user();
        $languageId = $authId ? $authId->language_id : null;
        $languages = Language::with('transLang')->get();
        $tags = BlogTag::where('deleted_at', null)
            ->where('language_id', $languageId)
            ->orderBy('name', 'asc')
            ->get();

        return [
            'languages' => $languages,
            'tags'      => $tags,
        ];
    }

    public function tagStore(Request $request): JsonResponse
    {
        BlogTag::create([
            'name'        => $request->name,
            'status'      => 1,
            'created_at'  => Carbon::now(),
            'language_id' => $request->language_id,
        ]);
        return response()->json([
            'code'    => 200,
            'message' => 'Blog Tag added successfully!'
        ], 200);
    }

    public function tagUpdate(Request $request, int $id): RedirectResponse
    {
        $tag = BlogTag::findOrFail($id);
        $tag->update([
            'name'   => $request->name,
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

    public function blogComments(): array
    {
        $comments = BlogReviews::join('blog_posts', 'blog_reviews.blog_id', '=', 'blog_posts.id')
            ->select('blog_reviews.*', 'blog_posts.title')
            ->where('blog_reviews.deleted_at', null)
            ->get();

        return [
            'comments' => $comments,
        ];
    }

    public function blogs(): array
    {
        /** @var \App\Models\User|null $authId */
        $authId = current_user();
        $languageId = $authId ? $authId->language_id : null;
        $languages = Language::with('transLang')->get();
        $blogPosts = BlogPost::join('users', 'blog_posts.created_by', '=', 'users.id')
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

        $categories = BlogCategory::where('deleted_at', null)
            ->where('language_id', $languageId)
            ->get();

        $tags = BlogTag::where('deleted_at', null)
            ->where('language_id', $languageId)
            ->get();

        return [
            'blogPosts'  => $blogPosts,
            'languages'  => $languages,
            'categories' => $categories,
            'tags'       => $tags,
        ];
    }

    public function blogDetails(string $id): array
    {
        $languages = Language::with('transLang')->get();
        $blogPosts = BlogPost::join('blog_categories', 'blog_posts.category', '=', 'blog_categories.id')
            ->leftJoin('blog_tags', 'blog_posts.tags', '=', 'blog_tags.id')
            ->select('blog_posts.*', 'blog_categories.name as category', 'blog_tags.name as tag')
            ->where('blog_posts.slug', $id)
            ->first();

        return [
            'blogPosts' => $blogPosts,
            'languages' => $languages,
        ];
    }

    public function blogAdd(): array
    {
        /** @var \App\Models\User|null $authId */
        $authId = current_user();
        $languageId = $authId ? $authId->language_id : null;
        $languages = Language::with('transLang')->where('deleted_at', null)->get();
        $tags = BlogTag::where('deleted_at', null)->where('language_id', $languageId)->where('status', '1')->get();
        $categories = BlogCategory::where('deleted_at', null)->where('language_id', $languageId)->where('status', '1')->get();

        return [
            'tags'       => $tags,
            'languages'  => $languages,
            'categories' => $categories,
        ];
    }

    public function blogStore(Request $request): JsonResponse
    {
        assert($request->file('image') instanceof \Illuminate\Http\UploadedFile);
        $imagePath = $this->imageResizer->uploadFile($request->file('image'), 'blogs/images', null);
        BlogPost::create([
            'title'       => $request->title,
            'slug'        => Str::slug($request->title),
            'language_id' => $request->language,
            'category'    => $request->category_id,
            'tags'        => json_encode($request->tag_id),
            'description' => $request->description,
            'image'       => $imagePath,
            'status'      => 1,
            'created_by'  => Auth::id(),
            'updated_by'  => Auth::id(),
            'created_at'  => Carbon::now(),
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

    public function blogEdit(int $id): array
    {
        /** @var \App\Models\User|null $authId */
        $authId = current_user();
        $languageId = $authId ? $authId->language_id : null;
        $blog = BlogPost::findOrFail($id);
        $languages = Language::with('transLang')->where('deleted_at', null)->get();
        $tags = BlogTag::where('deleted_at', null)->where('language_id', $languageId)->where('status', '1')->get();
        $categories = BlogCategory::where('deleted_at', null)->where('language_id', $languageId)->where('status', '1')->get();

        return [
            'blog'       => $blog,
            'tags'       => $tags,
            'languages'  => $languages,
            'categories' => $categories,
        ];
    }

    public function blogUpdate(Request $request, int $id): JsonResponse
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
                $imagePath = $this->imageResizer->uploadFile($request->file('image'), 'blogs/images', null);
                if (is_string($imagePath)) {
                    $blog->image = $imagePath;
                }
            }
        }
        $blog->save();
        return response()->json(['success' => true]);
    }
}
