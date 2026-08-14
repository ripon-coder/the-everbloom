<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\CheckoutCalculationRepository;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    protected CheckoutCalculationRepository $checkoutCalculationRepository;

    public function __construct(CheckoutCalculationRepository $checkoutCalculationRepository)
    {
        $this->checkoutCalculationRepository = $checkoutCalculationRepository;
    }

    /**
     * Display the shopping cart page.
     * Re-validates all session cart items directly against the database models.
     *
     * @return View
     */
    public function index(): View
    {
        $sessionCart = session('cart', []);
        $calculation = $this->checkoutCalculationRepository->calculate($sessionCart);

        $verifiedCart = $calculation['items'];
        session()->put('cart', $verifiedCart);

        return view('pages.cart.index', compact('verifiedCart', 'calculation'));
    }

    /**
     * Synchronize cart items with the database.
     * Always computes real prices & available stock directly from DB models.
     */
    public function sync(Request $request)
    {
        $inputCart = $request->input('cart', []);
        $calculation = $this->checkoutCalculationRepository->calculate($inputCart);

        $verifiedCart = $calculation['items'];
        $totalQuantity = array_sum(array_column($verifiedCart, 'quantity'));

        if ($totalQuantity > 30) {
            return response()->json([
                'success' => false, 
                'message' => 'You cannot add more than 30 products to your cart.'
            ], 422);
        }

        session()->put('cart', $verifiedCart);

        return response()->json([
            'success' => true,
            'cart' => $verifiedCart,
            'calculation' => $calculation,
            'has_inactive' => !empty($calculation['errors'])
        ]);
    }
}
