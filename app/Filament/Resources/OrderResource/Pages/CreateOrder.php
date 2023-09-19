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
        $client = $this->data['client_id'];
        $payment = $this->data['payment_id'];

        $available_order = Order::where('client_id', $client)
            ->where('status', 'draft')
            ->latest('id')
            ->first();
        if (!$available_order) {
            $available_order = Order::create([
                'status' => 'draft',
                'ordered_date' => '2023-08-23',
                'client_id' => $client,
                'payment_id' => $payment,
                'coupon_id' => 1,
            ]);
        }
    }

    protected function afterCreate(): void
    {
        $client = $this->data['client_id'];
        $product = $this->data['productId'];
        $quantity = $this->data['quantity'];
        $addons = $this->data['addonsId'];
        $removes = $this->data['removesId'];
        // dd($addons);
            $available_order = Order::where('client_id', $client)
            ->where('status', 'draft')
            ->latest('id')
            ->first();
    
        // if(!$old_product){
            $orderLine = OrderLine::create([
                'order_id' => $available_order->id,
                'product_id' => $product,
                'quantity' => $quantity,
            ]);
            $orderLine->products()->attach(['product_id'=>$product],['order_line_id'=>$orderLine->id]);
            // if(!empty($addons))
            // {
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
            // }
            // if(!empty($removes))
            // {
                if($removes !== null)
                {
                    foreach ($removes as $product_id)
                    {
                        $product_remove = ProductRemove::create([
                            'order_line_id' => $orderLine->id,
                        ]);
                        ProductProductRemove::create([
                            'product_id'=>$product_id,
                            'product_remove_id'=>[$product_remove->id],
                        ]);
                    }
                }else{
                    $msg = 'error';
                }
                
            // }
                
                
            
    }


}
