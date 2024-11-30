<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function showAdminCapability()
    {
        return view('admin.admin');
    }
    public function products()
    {
        $products = Product::all();
        return view('admin.products', compact('products'));
    }

    public function categories()
    {
        $categories = Category::all();
        return view('admin.categories', compact('categories'));
    }
}
