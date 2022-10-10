<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Services\CommentService;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $comments = DB::table('comments')->select('id', 'name', 'message')->whereNull('parent_id')->get();
        foreach ($comments as $index => $comment) {
            $comments[$index] = CommentService::generateChildren($comment);
        }
        return response()->json($comments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCommentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCommentRequest $request)
    {
        $comment = DB::table('comments')->insert([
            'name' => $request->name,
            'message' => $request->message,
            'parent_id' => $request->parent_id,
        ]);

        return response()->json($comment);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $comment = DB::table('comments')->find($id);
        $comment = CommentService::generateChildren($comment);
        return response()->json($comment);
    }

    /**
     * Update the specified resource in storage.
     *
     * \App\Http\Requests\UpdateCommentRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCommentRequest $request, $id)
    {
        $comment = DB::table('comments')
            ->where('id', $id)
            ->update([
                'name' => $request->name,
                'message' => $request->message,
            ]);
        return response()->json($comment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $comments = DB::table('comments')->where('id', $id)->delete();
        return response()->json($comments);
    }
}
