<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\CarInfo\Models\Tag;

class TagControlerController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('carinfo::tag.index');
    }

    
    /**
     * Save or update a tag in the database.
     *
     * This function validates the incoming request to ensure the 'tag' field is
     * required and unique. If validation fails, it returns a JSON response with
     * the validation errors. If the tag exists (identified by 'id'), it updates
     * the existing tag, otherwise, it creates a new tag record. On success, it 
     * returns a JSON response with a success message. In case of an exception, 
     * it returns a JSON response with an error message.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function save(Request $request){

        $validator = Validator::make($request->all(), [
            'tag' => 'required|unique:tags,tag,'.$request->id.',id,deleted_at,NULL',
        ],[
            'tag.required' => __('admin.rentals.tag_required'),
            'tag.unique' => __('admin.rentals.tag_unique'),
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors()->toArray()
            ],422);
        }
        $response = [];
        $successMessage = empty($request->id) ? __('admin.rentals.tag_create_success') : __('admin.rentals.tag_update_success');
        $errorMessage = empty($request->id) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            if($request->has('id') && $request->id == ""){
                $tag = new Tag();
            }else{
                $tag = Tag::find($request->id);
                $tag->status = $request->status == 'on' ? 1 : 0;
            }
            $tag->tag = $request->tag;
            $tag->save();
            $response = [
                'status' => 'success',
                'code'   => 200,
                'message' => $successMessage
            ];
        } catch (\Throwable $th) {
            $response = [
                'status' => 'error',
                'code'   => 422,
                'message' => $errorMessage
            ];
        }

        return response()->json($response);
    }

    /**
     * Get all tags.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTags(Request $request)
    {
        $tags = Tag::query();

        if($request->has('keyword') && $request->keyword != null){
            $tags->where('tag','like','%'.$request->keyword.'%');
        }

        if($request->has('status') && $request->status != null){
            $tags->where('status',$request->status);
        }

        $tags = $tags->orderBy('tag','asc')->get();
        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data' => $tags
        ]);
    }

    /**
     * Get a tag by its ID.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTag($id)
    {
        $response = '';
        try {
            $tag = Tag::find($id);
            $response = [
                'status'  => 'success',
                'code'    => 200,
                'data'    => $tag,
                'message' => 'Tag retrieved successfully'
            ];
        } catch (\Throwable $th) {
            $response = [
                'status'  => 'error',
                'code'    => 422,
                'data'    => null,
                'message' => $th->getMessage()
            ];
        }
        return response()->json($response);
    }

    /**
     * Delete a tag
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    
    public function deleteTag(Request $request)
    {
        
        try {
            $tag = Tag::findOrFail($request->delete_id);
            $tag->delete();
            $response = [
                'status' => 'success',
                'code'   => 200,
                'message'=> __('admin.rentals.tag_delete_success')
            ];
            return response()->json($response,200);
        }catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'message'=> __('admin.common.default_delete_error')
            ],422);
        } catch (\Throwable $th) {
            $response = [
                'status' => 'error',
                'code'   => 422,
                'message'=> __('admin.common.default_delete_error')
            ];
            return response()->json($response,422);
        }

    }
}
