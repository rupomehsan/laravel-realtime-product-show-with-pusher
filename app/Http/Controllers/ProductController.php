<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Product;
use App\Events\ProductUpdated;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{


    public function index()
    {
        $products = Product::latest()->get();
        return view('welcome', compact('products'));
    }

    public function addProduct(Request $request)
    {

        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description
        ]);

        broadcast(new ProductUpdated($product))->toOthers();
        return response()->json(['message' => 'Product added successfully!', 'product' => $product]);
    }


    public function getAddProduct()
    {
        return view('add-product');
    }
}
