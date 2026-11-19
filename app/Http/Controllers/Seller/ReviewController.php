<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $store = $request->user()->store;

        $distribution = $store->reviews()
            ->selectRaw('rating, count(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating');

        return view('seller.reviews.index', [
            'store' => $store,
            'reviews' => $store->reviews()->with(['buyer', 'order'])->latest()->paginate(10),
            'distribution' => $distribution,
            'count' => $distribution->sum(),
        ]);
    }
}
