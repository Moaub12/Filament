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
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;


    // protected function beforeSave():void
    // {
    //     dd($ordered);
    // }
    protected function afterSave():void 
    {
        $id = $this->data['id'];
        $data = $this->data['product'];
        $ordered = $this->data['order'];


        $available_order = Order::find($id);


        foreach ($data as $p)
        {
            $productId = $p['productId'];
            $quantity = $p['quantity'];
            $addons = $p['addonsId'];
            $removes = $p['removes'];


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
        if($ordered)
        {
            $available_order->status = "order";
            $available_order->update();
        }

    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
