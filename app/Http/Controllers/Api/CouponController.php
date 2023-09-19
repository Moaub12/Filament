<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Coupon;
use App\Models\Order;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function validateCoupon(Request $request)
    {
        $input = $request->input('code');

        if (is_null($input)) {
            return response()->json([
                'success' => false,
                'message' => 'null code',
            ]);
        }

        $coupon = Coupon::where('code', $input)->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon not found',
                'status' => 'not found',
            ]);
        }
        
               // check if the client used the coupon before 
        $usedCoupon = Order::where('client_id',$request->input('id'))
            ->where('coupon_id', $coupon->id)
            ->first();

        if ($usedCoupon) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon has already been used by this client',
                'status'=>'used'
            ]);
        }
        $currentDate = now(); // Current date and time

        if ($coupon->activation_date > $currentDate) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon has not been activated yet',
                'status' => 'unactivated',
            ]);
        }

        if ($coupon->expire_date < $currentDate) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon has expired',
                'status' => 'expired', 
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Coupon is valid',
            'data'    => $coupon,
            'status' => 'valid',
        ]);
    }
   
}
