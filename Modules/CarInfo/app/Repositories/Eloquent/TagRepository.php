<?php

namespace Modules\CarInfo\Repositories\Eloquent;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Modules\CarInfo\Models\Tag;
use Modules\CarInfo\Repositories\Contracts\TagRepositoryInterface;

class TagRepository implements TagRepositoryInterface
{
    public function store(Request $request): array
    {
        $id = $request->input('id');

        $successMessage = $id
            ? __('admin.rentals.tag_update_success')
            : __('admin.rentals.tag_create_success');

        $errorMessage = $id
            ? __('admin.common.default_update_error')
            : __('admin.common.default_create_error');

        try {
            $status = $request->input('status') === 'on' ? 1 : 0;

            $data = [
                'tag'    => $request->input('tag'),
                'status' => $id ? $status : 1
            ];

            Tag::updateOrCreate(['id' => $id], $data);

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => $successMessage,
            ];
        } catch (\Throwable $th) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => $errorMessage,
                'error'   => $th->getMessage(),
            ];
        }
    }

    public function getAll(Request $request): array
    {
        try {
            $tags = Tag::query();

            if ($request->has('keyword') && $request->keyword != null) {
                $tags->where('tag', 'like', '%' . $request->keyword . '%');
            }

            if ($request->has('status') && $request->status != null) {
                $tags->where('status', $request->status);
            }

            $tags = $tags->orderBy('tag', 'asc')->get();

            return [
                'status' => 'success',
                'code'   => 200,
                'data' => $tags
            ];
        } catch (\Exception $e) {
            return [
                'code' => 500,
                'message' => __('admin.common.default_retrieve_error'),
            ];
        }
    }

    public function getById(int $id): array
    {
        $data = Tag::find($id);

        if (!$data) {
            return [
                'status' => 'error',
                'code'   => 404,
                'message' => __('admin.common.no_data_found')
            ];
        }

        return [
            'status'  => 'success',
            'code'    => 200,
            'data'    => $data,
        ];
    }

    public function delete(int $id): array
    {
        try {
            /** @var \Modules\CarInfo\Models\Tag $tag */
            $tag = Tag::findOrFail($id);
            $tag->delete();

            return [
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.rentals.tag_delete_success')
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'status' => 'error',
                'code'   => 404,
                'message' => __('admin.common.no_data_found'),
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'error',
                'code'   => 500,
                'message' => __('admin.common.default_delete_error'),
            ];
        }
    }
}