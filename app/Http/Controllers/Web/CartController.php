<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $cartItems = [];
        $total = 0;

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product && $product->is_active && !$product->is_expired) {
                $itemTotal = $item['quantity'] * $item['price'];
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $itemTotal,
                ];
                $total += $itemTotal;
            }
        }

        return view('web.cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::where('is_active', true)
            ->where('is_expired', false)
            ->findOrFail($request->product_id);

        // Check stock if tracking batches
        if ($product->track_batch) {
            $availableStock = $product->total_stock;
            if ($availableStock < $request->quantity) {
                return back()->with('error', "Only {$availableStock} {$product->selling_unit}(s) available in stock.");
            }
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $request->quantity;
        } else {
            $cart[$product->id] = [
                'name' => $product->name,
                'price' => $product->selling_price,
                'quantity' => $request->quantity,
                'image' => $product->images && is_array($product->images) && count($product->images) > 0 
                    ? $product->images[0] 
                    : null,
            ];
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Product added to cart!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $product = Product::find($id);
            
            // Check stock if tracking batches
            if ($product && $product->track_batch) {
                $availableStock = $product->total_stock;
                if ($availableStock < $request->quantity) {
                    return back()->with('error', "Only {$availableStock} {$product->selling_unit}(s) available in stock.");
                }
            }

            $cart[$id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Cart updated!');
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);
        unset($cart[$id]);
        session()->put('cart', $cart);

        return back()->with('success', 'Product removed from cart!');
    }

    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('cart.index')->with('success', 'Cart cleared!');
    }
}
