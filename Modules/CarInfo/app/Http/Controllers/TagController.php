<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\CarInfo\Http\Requests\TagRequest;
use Modules\CarInfo\Models\Tag;
use Modules\CarInfo\Repositories\Contracts\TagRepositoryInterface;

class TagController extends Controller
{
    protected TagRepositoryInterface $tagRepository;

    public function __construct(TagRepositoryInterface $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        return view('carinfo::tag.index');
    }

    /**
     * Save or update a tag in the database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(TagRequest $request): JsonResponse
    {
        $response = $this->tagRepository->store($request);
        return response()->json($response, $response['code']);
    }

    /**
     * Get all tags.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTags(Request $request): JsonResponse
    {
        $response = $this->tagRepository->getAll($request);
        return response()->json($response, $response['code']);
    }

    /**
     * Get a tag by its ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTag($id): JsonResponse
    {
        $response = $this->tagRepository->getById($id);
        return response()->json($response, $response['code']);
    }

    /**
     * Delete a tag
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteTag(Request $request): JsonResponse
    {
        $id = $request->delete_id;
        $response = $this->tagRepository->delete($id);
        return response()->json($response, $response['code']);
    }
}
