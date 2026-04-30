<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\ProductService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProductController extends Controller
{
    protected $productService;

    /**
     * ProductController constructor.
     *
     * @param ProductService $productService
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Display the shop page.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $products = $this->productService->getShopProducts($request->all());
        $categories = $this->productService->getActiveCategories();

        return view('pages.shop', compact('products', 'categories'));
    }

    /**
     * Display the specified product.
     *
     * @param string|null $slug
     * @return View|RedirectResponse
     */
    public function show(?string $slug = null)
    {
        if (!$slug) {
            return redirect()->route('home');
        }

        $product = $this->productService->getProductBySlug($slug);
        $relatedProducts = $this->productService->getRelatedProducts($product);

        return view('pages.product', compact('product', 'relatedProducts'));
    }
}
