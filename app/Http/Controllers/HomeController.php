<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::where('active', true)->orderBy('sort_order')->get();
        return view('home', compact('products'));
    }

    public function privacy()
    {
        return view('pages.privacy');
    }

    public function shipping()
    {
        return view('pages.shipping');
    }

    public function faq()
    {
        return view('pages.faq');
    }
}
