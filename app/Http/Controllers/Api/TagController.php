<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {

        $tags=Tag::all();
        return response()->json([
            'success'=>true,
            'message'=>'all Tags',
            'data'=>$tags,
        ]);
    }
    public function productsByTag($tagId)
    {
        // Find the category by its ID
        $tag = Tag::find($tagId);

        if (!$tag) {
            return response()->json(['message' => 'Tag not found'], 404);
        }

        // Retrieve products belonging to the specified category
        $products = $tag->products;

        return response()->json($products);
    }
}
