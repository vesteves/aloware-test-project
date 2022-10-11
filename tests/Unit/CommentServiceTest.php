<?php

namespace Tests\Unit;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;

use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Services\CommentService;
use Illuminate\Support\Facades\DB;

class CommentServiceTest extends TestCase
{

    use RefreshDatabase;

    public function test_should_pass_verifyParent()
    {
        $id = DB::table('comments')->insertGetId([
            'name' => 'some name',
            'message' => 'some message',
        ]);

        $result = CommentService::verifyParent($id);


        $this->assertTrue($result);
    }

    public function test_should_not_pass_verifyParent()
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

        $result = CommentService::verifyParent($third);


        $this->assertNotTrue($result);
    }

    public function test_should_generateChildren_empty()
    {
        $id = DB::table('comments')->insertGetId([
            'name' => 'some name',
            'message' => 'some message',
        ]);

        $comment = DB::table('comments')->find($id);

        $result = CommentService::generateChildren($comment);

        $this->assertEquals($result, [
            'id' => $id,
            'name' => 'some name',
            'message' => 'some message',
            'comments' => [],
        ]);
    }

    public function test_should_generateChildren()
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

        $comment = DB::table('comments')->find($first);

        $result = CommentService::generateChildren($comment);

        $this->assertEquals($result, [
            'id' => $first,
            'name' => 'some name',
            'message' => 'some message',
            'comments' => [
                [
                    'id' => $second,
                    'name' => 'some name',
                    'message' => 'some message',
                    'comments' => [],
                ]
            ],
        ]);
    }
}
