<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CommentRequest;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $comments = DB::table('comments')->get();
        return response()->json($comments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CommentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CommentRequest $request)
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
        $comments = DB::table('comments')->find($id);
        return response()->json($comments);
    }

    /**
     * Update the specified resource in storage.
     *
     * \App\Http\Requests\CommentRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CommentRequest $request, $id)
    {
        $comment = DB::table('comments')
            ->where('id', $id)
            ->update([
                'name' => $request->name,
                'message' => $request->message,
                'parent_id' => $request->parent_id,
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
