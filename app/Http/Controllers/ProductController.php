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
        // $products = Product::latest()->get();
        $response = Http::get('https://fakestoreapi.com/products');
        $products =  $response->json();
        return view('welcome', compact('products'));
    }

    public function getAddProduct()
    {
        $response = Http::get('https://fakestoreapi.com/products');
        $products =  $response->json();
        return view('add-product', compact('products'));
    }

    public function createOrUpdateProduct(Request $request)
    {
        try {
            $productData = [
                'title' => $request->title,
                'price' => $request->price,
                'description' => $request->description
            ];

            $url = $request->id
                ? 'https://fakestoreapi.com/products/' . $request->id
                : 'https://fakestoreapi.com/products';

            $response = $request->id
                ? $this->updateProduct($url, $productData)
                : $this->createProduct($url, $productData);

            return $response;
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    private function updateProduct($url, $productData)
    {
        $response = Http::put($url, $productData);

        return $this->handleApiResponse($response, 'Product updated successfully!');
    }

    private function createProduct($url, $productData)
    {
        $response = Http::post($url, $productData);

        return $this->handleApiResponse($response, 'Product added successfully!');
    }

    private function handleApiResponse($response, $successMessage)
    {
        if ($response->successful()) {
            $latestProduct = $response->json();
            unset($latestProduct['id']);
            $Product = Product::create($latestProduct);
            broadcast(new ProductUpdated($Product))->toOthers();
            return response()->json(['message' => $successMessage, 'product' => $Product]);
        }

        dd($response->json());


        return response()->json(['message' => 'Failed to process the product. Please try again.']);
    }
}
