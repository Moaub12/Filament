<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;




class ProductController extends Controller
{
    public function index()
    {

        $products=Product::where('feature','product')->get();
        return response()->json([
            'success'=>true,
            'message'=>'all products',
            'data'=>$products,
        ]);
    }
    public function show($id)
    {
        $product=Product::find($id);
        
        if(is_null($product)){
            return response()->json([
                'success'=>false,
                'message'=>'failed to fetch product',
                
            ]);
        }
        return response()->json([
            'success'=>true,
            'message'=>'product fetched success',
            'data'=>$product,
            
        ]);
    }

    public function getProductsOrderedByCounter()
    {
        $products = Product::where('feature','product')->orderByOrderedCounterDescending()->get();

        return response()->json($products);
    }
   
    public function filterByPrice(Request $request)
    {
        $minPrice = $request->input('min_price', 0);
        $maxPrice = $request->input('max_price', PHP_FLOAT_MAX);
    
        // Retrieve products with the "product" feature
        $products = Product::where('feature', 'product')
                           ->whereBetween('price', [$minPrice, $maxPrice])
                           ->get();
    
        return response()->json($products);
    }
    

}
