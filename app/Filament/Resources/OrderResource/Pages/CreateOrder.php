<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Product;
use App\Models\ProductAddon;
use App\Models\ProductProductAddon;
use App\Models\ProductProductRemove;
use App\Models\ProductRemove;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function beforeCreate():void
    {
        // Repeater
        // $data = $this->data['product'];
        // foreach ($data as $p) {
        //     dd($p);
        // }

        
        
        
    }

    protected function afterCreate(): void
    {
        $client = $this->data['client_id'];
        // Repeater
        $data = $this->data['product'];
        foreach ($data as $p) {
            $productId = $p['productId'];
            $quantity = $p['quantity'];
            $addons = $p['addonsId'];
            $removes = $p['removes'];
            $addons_price = 0;
            $subtotal = 0;

            $product = Product::find($productId);          
            
            $available_order = Order::where('client_id', $client)
            ->where('status', 'draft')
            ->latest('id')
            ->first();
    
        // if(!$old_product){
            $orderLine = OrderLine::create([
                'order_id' => $available_order->id,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
            $orderLine->products()->attach(['product_id'=>$productId],['order_line_id'=>$orderLine->id]);
                if ($addons !== null)
                {
                    foreach ($addons as $product_id)
                    {
                        $product_addon = ProductAddon::create([
                            'order_line_id' => $orderLine->id,
                        ]);
                        // dd($product_addon->id);
                        ProductProductAddon::create([
                            'product_id'=>$product_id,
                            'product_addon_id'=>$product_addon->id,
                        ]);
                        $addon = Product::find($product_id);
                        $addon_name=$addon->name;
                        $addons_price += $addon->price;
                      
                         $orderLine->addons= $orderLine->addons.$addon_name.",";
                         $orderLine->save();
                    }
                    $orderLine->subTotal = ($product->price + $addons_price) * $quantity;
                    $orderLine->save();


                }else{
                    $msg = 'error';
                }
                if($removes !== null)
                {
                    foreach ($removes as $product_id)
                    {
                        $product_remove = ProductRemove::create([
                            'order_line_id' => $orderLine->id,
                        ]);
                        ProductProductRemove::create([
                            'product_id'=>$product_id,
                            'product_remove_id'=>$product_remove->id,
                        ]);
                        $remove_name=Product::find($product_id)->name;
                      
                        $orderLine->removes= $orderLine->removes.$remove_name.",";
                        $orderLine->save();
                    }
                }else{
                    $msg = 'error';
                }
        }
                
                
            
    }


}
