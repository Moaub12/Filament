<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {

        $categories=Category::all();
        return response()->json([
            'success'=>true,
            'message'=>'all categories',
            'data'=>$categories,
        ]);
    }
    public function productsByCategory($categoryId)
    {
        // Find the category by its ID
        $category = Category::find($categoryId);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        // Retrieve products belonging to the specified category
        $products = $category->products()->where('feature','product')->get();

        return response()->json($products);
    }
}
