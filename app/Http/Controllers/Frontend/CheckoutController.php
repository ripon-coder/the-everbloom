<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Contracts\CheckoutCalculationRepository;

class CheckoutController extends Controller
{
    protected $checkoutCalculationRepository;

    public function __construct(CheckoutCalculationRepository $checkoutCalculationRepository)
    {
        $this->checkoutCalculationRepository = $checkoutCalculationRepository;
    }

    public function calculate(Request $request)
    {
        $cart = $request->input('cart', []);
        $shippingMethod = $request->input('shipping_method', 'inside_dhaka');
        $couponCode = $request->input('coupon_code');

        $result = $this->checkoutCalculationRepository->calculate($cart, $shippingMethod, $couponCode);

        return response()->json([
            'success' => true,
            'data' => $result,
            'has_errors' => !empty($result['errors'])
        ]);
    }
}
