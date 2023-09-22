<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderLine;
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
                    }
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
                    }
                }else{
                    $msg = 'error';
                }
        }
                
                
            
    }


}
