<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class CommentTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_should_store_comment()
    {
        $response = $this->postJson('/api/comments', [
            'name' => 'some name',
            'message' => 'some message',
            'parent_id' => null,
        ]);

        $response
            ->assertOk()
            ->assertSee("true");
    }

    public function test_should_store_child_comment()
    {
        $parent = DB::table('comments')->insertGetId([
            'name' => 'some name',
            'message' => 'some message',
        ]);

        $response = $this->postJson('/api/comments', [
            'name' => 'some name',
            'message' => 'some message',
            'parent_id' => $parent,
        ]);

        $response
            ->assertOk()
            ->assertSee("true");
    }

    public function test_should_store_child_child_comment()
    {
        $first = DB::table('comments')->insertGetId([
            'name' => 'some name',
            'message' => 'some message',
        ]);

        $second = DB::table('comments')->insertGetId([
            'name' => 'some name',
            'message' => 'some message',
            'parent_id' => $first,
        ]);

        $response = $this->postJson('/api/comments', [
            'name' => 'some name',
            'message' => 'some message',
            'parent_id' => $second,
        ]);

        $response
            ->assertOk()
            ->assertSee("true");
    }

    public function test_should_not_store_comment()
    {
        $first = DB::table('comments')->insertGetId([
            'name' => 'some name',
            'message' => 'some message',
        ]);

        $second = DB::table('comments')->insertGetId([
            'name' => 'some name',
            'message' => 'some message',
            'parent_id' => $first,
        ]);

        $third = DB::table('comments')->insertGetId([
            'name' => 'some name',
            'message' => 'some message',
            'parent_id' => $second,
        ]);

        $response = $this->postJson('/api/comments', [
            'name' => 'some name',
            'message' => 'some message',
            'parent_id' => $third,
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                "message" => "The max layer is 3",
                "errors" => [
                    "parent_id" => [
                        "The max layer is 3"
                    ]
                ]
            ]);
    }

    public function test_should_update_comment()
    {
        $comment = DB::table('comments')->insertGetId([
            'name' => 'some name',
            'message' => 'some message',
            'parent_id' => null,
        ]);

        $response = $this->put('/api/comments/' . $comment, [
            'name' => 'some name change',
            'message' => 'some message  change',
        ]);

        $response
            ->assertOk()
            ->assertSee("1");
    }

    public function test_should_remove_comment()
    {
        $comment = DB::table('comments')->insertGetId([
            'name' => 'some name',
            'message' => 'some message',
            'parent_id' => null,
        ]);

        $response = $this->delete('/api/comments/' . $comment);

        $response
            ->assertOk()
            ->assertSee("1");
    }

    public function test_should_get_comment()
    {
        $comment = DB::table('comments')->insertGetId([
            'name' => 'some name',
            'message' => 'some message',
            'parent_id' => null,
        ]);

        $response = $this->get('/api/comments/' . $comment);

        $response
            ->assertOk()
            ->assertJson([
                'id' => $comment,
                'name' => 'some name',
                'message' => 'some message',
                'comments' => [],
            ]);
    }

    public function test_should_get_comment_with_child()
    {
        $parent = DB::table('comments')->insertGetId([
            'name' => 'some name',
            'message' => 'some message',
            'parent_id' => null,
        ]);

        $child = DB::table('comments')->insertGetId([
            'name' => 'some name child',
            'message' => 'some message child',
            'parent_id' => $parent,
        ]);

        $response = $this->getJson('/api/comments/' . $parent);

        $response
            ->assertOk()
            ->assertJson([
                'id' => $parent,
                'name' => 'some name',
                'message' => 'some message',
                'comments' => [
                    [
                        'id' => $child,
                        'name' => 'some name child',
                        'message' => 'some message child',
                        'comments' => [],
                    ]
                ],
            ]);
    }

    public function test_should_get_all_comments()
    {
        $first = DB::table('comments')->insertGetId([
            'name' => 'some name',
            'message' => 'some message',
            'parent_id' => null,
        ]);

        $second = DB::table('comments')->insertGetId([
            'name' => 'some name',
            'message' => 'some message',
            'parent_id' => null,
        ]);

        $response = $this->get('/api/comments');

        $response
            ->assertOk()
            ->assertJsonCount(2);
    }
}
