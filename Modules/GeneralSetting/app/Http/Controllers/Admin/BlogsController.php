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
        return $this->blogRepository->categoryStore($request);
    }

    public function categoryUpdate(CategoryUpdateRequest $request, int $id): RedirectResponse
    {
        return $this->blogRepository->categoryUpdate($request, $id);
    }

    public function categoryDestroy(int $id): RedirectResponse
    {
        return $this->blogRepository->categoryDestroy($id);
    }

    public function blogTags(): View
    {
        $data = $this->blogRepository->blogTags();
        return view('generalsetting::cms.blogs.blog-tags', [...$data]);
    }

    public function tagStore(BlogTagRequest $request): JsonResponse
    {
        return $this->blogRepository->tagStore($request);
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
        return $this->blogRepository->tagUpdate($request, $id);
    }

    public function tagDestroy(int $id): RedirectResponse
    {
        return $this->blogRepository->tagDestroy($id);
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
        return $this->blogRepository->blogStore($request);
    }

    public function blogDestroy(int $id): JsonResponse
    {
        return $this->blogRepository->blogDestroy($id);
    }

    public function blogEdit(int $id): View
    {
        $data = $this->blogRepository->blogEdit($id);
        return view('generalsetting::cms.blogs.edit-blog', [...$data]);
    }

    public function blogUpdate(Request $request, int $id): JsonResponse
    {
        return $this->blogRepository->blogUpdate($request, $id);
    }
}
