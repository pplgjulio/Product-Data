<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('home');
});

Route::post('/submit', function (Request $request) {
    $data = $request->validate([
        'product_code' => 'required|string|max:255',
        'price' => 'required|numeric|min:0.01',
        'quantity' => 'required|integer|min:1',
    ]);

    session()->push('products', $data);

    return response()->json(['message' => 'Product added successfully']);
});

Route::post('/delete', function (Request $request) {
    $index = $request->input('index');
    $products = session('products', []);

    if (isset($products[$index])) {
        unset($products[$index]);
        $products = array_values($products);
        session(['products' => $products]);

        return response()->json(['message' => 'Product deleted successfully.']);
    }

    return response()->json(['message' => 'Product not found.'], 404);
});

Route::post('/edit', function (Request $request) {
    $data = $request->validate([
        'index' => 'required|integer',
        'product_code' => 'required|string|max:255',
        'price' => 'required|numeric|min:0.01',
        'quantity' => 'required|integer|min:1',
    ]);

    $products = session('products', []);
    $index = $data['index'];

    if (!isset($products[$index])) {
        return response()->json(['error' => 'Product not found.'], 404);
    }

    $products[$index] = [
        'product_code' => $data['product_code'],
        'price' => $data['price'],
        'quantity' => $data['quantity'],
    ];

    session(['products' => $products]);

    return response()->json(['message' => 'Product updated successfully']);
});
