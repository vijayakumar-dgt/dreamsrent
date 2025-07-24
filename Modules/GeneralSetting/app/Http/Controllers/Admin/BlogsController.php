<?php

namespace Modules\GeneralSetting\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\GeneralSetting\Http\Requests\BlogCategoryRequest;
use Modules\GeneralSetting\Http\Requests\BlogPostRequest;
use Modules\GeneralSetting\Http\Requests\BlogTagRequest;
use Modules\GeneralSetting\Http\Requests\CategoryUpdateRequest;
use Modules\GeneralSetting\Http\Requests\TagUpdateRequest;
use Modules\GeneralSetting\Repositories\Contracts\BlogCategoryRepositoryInterface;

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

    public function categoryUpdate(CategoryUpdateRequest $request, int $id): RedirectResponse
    {
        $response = $this->blogRepository->categoryUpdate($request, $id);
        return $response;
    }

    public function categoryDestroy(int $id): RedirectResponse
    {
        $response = $this->blogRepository->categoryDestroy($id);
        return $response;
    }

    public function blogTags(): View
    {
        $data = $this->blogRepository->blogTags();
        return view('generalsetting::cms.blogs.blog-tags', [...$data]);
    }

    public function tagStore(BlogTagRequest $request): JsonResponse
    {
        $response = $this->blogRepository->tagStore($request);
        return $response;
    }

    /**
     * Update the tag with the given ID.
     *
     * @param \Illuminate\Http\Request $request The HTTP request instance.
     * @param int $id The ID of the tag to update.
     * @return \Illuminate\Http\RedirectResponse Redirect response after updating the tag.
     */
    public function tagUpdate(TagUpdateRequest $request, $id): RedirectResponse
    {
        $response = $this->blogRepository->tagUpdate($request, $id);
        return $response;
    }

    public function tagDestroy(int $id): RedirectResponse
    {
        $response = $this->blogRepository->tagDestroy($id);
        return $response;
    }

    public function blogComments(): View
    {
        $data = $this->blogRepository->blogComments();
        return view('generalsetting::cms.blogs.blog-comments', [...$data]);
    }

    public function blogs(): View
    {
        $data = $this->blogRepository->blogs();
        return view('generalsetting::cms.blogs.blogs', [...$data]);
    }

    public function blogDetails(string $id): View
    {
        $data = $this->blogRepository->blogDetails($id);
        return view('generalsetting::cms.blogs.blog-details', [...$data]);
    }

    public function blogAdd(): View
    {
        $data = $this->blogRepository->blogAdd();
        return view('generalsetting::cms.blogs.add-blog', [...$data]);
    }

    public function blogStore(BlogPostRequest $request): JsonResponse
    {
        $response = $this->blogRepository->blogStore($request);
        return $response;
    }

    public function blogDestroy(int $id): JsonResponse
    {
        $response = $this->blogRepository->blogDestroy($id);
        return $response;
    }

    public function blogEdit(int $id): View
    {
        $data = $this->blogRepository->blogEdit($id);
        return view('generalsetting::cms.blogs.edit-blog', [...$data]);
    }

    public function BlogUpdate(Request $request, int $id): JsonResponse
    {
        $response = $this->blogRepository->BlogUpdate($request, $id);
        return $response;
    }
}
