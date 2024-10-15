<?php

namespace App\Http\Controllers;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index($model, $model_id): \Illuminate\Http\JsonResponse
    {
        $comment = Comment::where('model', $model)->where('model_id', $model_id)->first();
        return ApiResponse::success($comment, ApiMessage::COMMENT_LIST);
    }

    public function store($model, $model_id, Request $request): \Illuminate\Http\JsonResponse
    {

        $input = $request->all();
        $oldComments = Comment::where('model', $model)->where('model_id', $model_id)->first();
        if($oldComments){
            if ($input['content'] == '') {
                $oldComments->delete();
                return ApiResponse::success(null, ApiMessage::COMMENT_STORE_SUCCESS);
            }
            $oldComments->update(['content' => $input['content'], 'user_id' => auth()->user()->id]);
            return ApiResponse::success($oldComments, ApiMessage::COMMENT_STORE_SUCCESS);
        } else
            $comment = Comment::create([
                'content' => $input['content'],
                'model' => $model,
                'model_id' => $model_id,
                'user_id' => auth()->user()->id
            ]);

        return ApiResponse::success($comment, ApiMessage::COMMENT_STORE_SUCCESS);
    }
}
