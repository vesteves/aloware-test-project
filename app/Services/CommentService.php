<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;

class CommentService {

    /**
     * Creating the parent and children relationship.
     *
     * @param  object  $parent
     * @return object  $parent
     */
    static function generateChildren ($parent) {
        $children = DB::table('comments')->where('parent_id', $parent->id)->get();
        $parent->comments = [];
        foreach ($children as $child) {
            if ($child->parent_id !== null) {
                $child->comments = [];
                $child = self::generateChildren($child);
            }
            array_push($parent->comments, $child);
        }

        $result = [
            'id' => $parent->id,
            'name' => $parent->name,
            'message' => $parent->message,
            'comments' => $parent->comments,
        ];

        return $result;
    }
}
