<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\OrderLine;
use App\Models\Product;
use App\Models\ProductAddon;
use App\Models\ProductOrderline;
use App\Models\ProductProductAddon;
use App\Models\ProductProductRemove;
use App\Models\ProductRemove;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'product' => 'required',
            'quantity' => 'required',
            'add' => 'array',
            'remove'=>'array',
            'client_id'=>'required',
        ]);
        // $user_id = auth()->user()->id;
        $productId = $request->input('product');
        $quantity = $request->input('quantity');
        $addons = $request->input('add', []);
        $removes = $request->input('remove',[]);
        $client=$request->input('client_id');
        $payment = $request->input('payment_id');
        $subtotal = 0;
        
        $order = Order::where('client_id',$client )
            ->where('status', 'draft')
            ->latest('id')
            ->first();
        
        if (!$order) {
            $order = Order::create([
                'status' => 'draft',
                // 'client_id' => $user_id,
                'ordered_date'=>"2023-08-23",
                'payment_id'=>$payment,
                'client_id'=>$client,
                'total'=>0,
            ]
        );
            OrderDetails::create([
                'order_id'=>$order->id,
            ]);
        }
        $total = $order->total;


        $pro = Product::find($productId);
        $subtotal+=$pro->price;


        $orderLine = OrderLine::create([
            'order_id' => $order->id,
            'product_id' => $productId,
            'quantity' => $quantity,
            'subTotal'=>$subtotal
            
        ]);
        $orderLine->products()->attach(['product_id'=>$productId],['order_line_id'=>$orderLine->id]);
        
        foreach ($addons as $product_id) {
            $product_addon = ProductAddon::create([
                'order_line_id' => $orderLine->id,
            ]);
            ProductProductAddon::create([
                'product_id'=>$product_id,
                'product_addon_id'=>$product_addon->id,
            ]);
            $pro = Product::find($product_id);
            $subtotal+= $pro->price;
        };
        //orderline total price
        $orderLine->subTotal = $subtotal * $orderLine->quantity;
        $orderLine->save();

        //order total price
        $total += $orderLine->subTotal;
        $order->total = $total;
        $order->save();

        foreach ($removes as $product_id) {
            $product_remove = ProductRemove::create([
                'order_line_id' => $orderLine->id,
            ]);
            ProductProductRemove::create([
                'product_id'=>$product_id,
                'product_remove_id'=>$product_remove->id,
            ]);
        }
        $products = $order->products;
        
        return response()->json(['message'=>'Product Added To Draft-Order','products'=>$products]);
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'fname'=>'required',
            'lname'=>'required',
            'country_region'=>'required',
            'adress'=>'required',
            'town_city'=>'required',
            'state'=>'required',
            'zip_code'=>'required',
            'phone'=>'required',
            'email'=>'required',

        ]);

        // $user_id = auth()->user()->id;
        $client = $request->input('client_id');
        $order = Order::where('client_id', $client)
        ->where('status', 'draft')
        ->latest('id')
        ->first();

        if($request->has('coupon'))
        {
            $coupon = $request->coupon;
            $order->coupon_id = $request->input('coupon.id');
            $order->total_discounted = $order->total-$order->total *$request->input('coupon.discount')/100;
            $order->save();

        }

        if($order)
        {
            $details = OrderDetails::where('order_id',$order->id)->first();
            if($details)
            {
                $details->fname = $request->input('fname');
                $details->lname = $request->input('lname');
                $details->company_name = $request->input('company_name');
                $details->country_region = $request->input('country_region');
                $details->adress = $request->input('adress');
                $details->town_city = $request->input('town_city');
                $details->state = $request->input('state');
                $details->zip_code = $request->input('zip_code');
                $details->phone = $request->input('phone');
                $details->email = $request->input('email');
                $details->notes = $request->input('notes');

                $details->save();

                $order->status = 'order';
                $order->save();

                $orderlines = OrderLine::where('order_id',$order->id)->get();
                foreach($orderlines as $orderline)
                {
                    $product = $orderline->product;
                    $product->ordered_counter +=$orderline->quantity;
                    $product->save();
                }
                return response()->json([
                    'message'=>'Ordered',
                    'order_id'=>$order->id,
                    'details'=>$details,
                ],200);
            }
            return response()->json([
                'message'=>'An Error Occurred, Details Added Failed'
            ],500);
        }
    }

    public function products(Request $request)
    {
        $client = $request->input('client_id');
        $order = Order::where('client_id', $client)
        ->where('status', 'draft')
        ->latest('id')
        ->first(); 
        if($order)
        {
            $products = OrderLine::where('order_id',$order->id)->with('product')->get();
            if($products)
            {
                return response()->json([
                    'message'=>'Products In Order',
                    'products'=>$products
                ],200);
            }
            return response()->json([
                'message'=>'Empty Order',
            ],404);
        }
        return response()->json([
            'message'=>'No Order',
        ],404);
        
    }

    public function removeProduct( $orderline_id, Request $request)
    {
        $client = $request->input('client_id');
        $order = Order::where('client_id', $client)
        ->where('status', 'draft')
        ->latest('id')
        ->first(); 
        
        if($order)
        {
            $orderline = OrderLine::where('order_id',$order->id)->where('id',$orderline_id)->first();
            if($orderline) 
            {
                $order->total -= $orderline->subTotal;
                $order->save();
                //addons
                foreach(ProductAddon::where('order_line_id', $orderline->id)->get() as $addon)
                {
                    ProductProductAddon::where('product_addon_id',$addon->id)->delete();
                    $addon->delete();
                }

                //removes
                foreach(ProductRemove::where('order_line_id', $orderline->id)->get() as $remove)
                {
                    ProductProductRemove::where('product_remove_id',$remove->id)->delete();
                    $remove->delete();
                }

                ProductOrderline::where('order_line_id',$orderline->id)->delete();
                $orderline->delete();
                $orderlines = OrderLine::where('order_id',$order->id)->get();
                if($orderlines->isEmpty())
                {
                    OrderDetails::where('order_id',$order->id)->delete();
                    $order->delete();
                }
                return response()->json([
                    'message' => 'Orderline removed from the order.',
                ], 200);
            }
            return response()->json([
                'message' => 'Orderline not found in the order.',
            ], 404);
        }
        return response()->json([
        'message' => 'No Order',
        ], 404);
        
    }

    public function getAddRemoveProduct($id)
    {
        $product=Product::find($id);
        if($product)
        {
            $product->addons;
            $product->removables;
            return response()->json([
                'message'=>'Product with his addons and removes',
                'product'=>$product
            ]);
        }
        return response()->json([
            'message'=>'Product not found'
        ]);
    }
}
