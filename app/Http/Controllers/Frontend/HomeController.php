<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\HomeService;
use Illuminate\View\View;

class HomeController extends Controller
{
    protected $homeService;

    /**
     * HomeController constructor.
     *
     * @param HomeService $homeService
     */
    public function __construct(HomeService $homeService)
    {
        $this->homeService = $homeService;
    }

    /**
     * Display the home page.
     *
     * @return View
     */
    public function index(): View
    {
        $data = $this->homeService->getHomeData();
        return view('pages.home', $data);
    }
}
