<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;

class CommentService {

    /**
     * Creating the parent and children relationship.
     *
     * @param  object  $parent
     * @return array  $result
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

    static function verifyParent ($parent_id, $count = 1) {
        $parent = DB::table('comments')->find($parent_id);
        if ($parent->id) {
            $count++;
            if ($count > 3) {
                return false;
            }
            // throw_if($count > 3, "The max layer is 3");
            if ($parent->parent_id) {
                return self::verifyParent($parent->parent_id, $count);
            }
        }
        return true;
    }
}
