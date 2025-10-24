<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = CartItem::with('product')
            ->active()
            ->forUser(auth()->user())
            ->get();

        $total = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        return view('cart.index', compact('cartItems', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::active()->findOrFail($request->product_id);

        if (!$product->hasStock($request->quantity)) {
            return back()->with('error', 'Insufficient stock available.');
        }

        DB::transaction(function () use ($request, $product) {
            $cartItem = CartItem::active()
                ->forUser(auth()->user())
                ->where('product_id', $product->id)
                ->first();

            if ($cartItem) {
                $newQuantity = $cartItem->quantity + $request->quantity;
                
                if (!$product->hasStock($newQuantity)) {
                    throw new \Exception('Insufficient stock available.');
                }

                $cartItem->update([
                    'quantity' => $newQuantity,
                    'expires_at' => now()->addMinutes(20),
                ]);
            } else {
                CartItem::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'session_id' => auth()->check() ? null : session()->getId(),
                    'quantity' => $request->quantity,
                    'expires_at' => now()->addMinutes(20),
                ]);
            }
        });

        return back()->with('success', 'Product added to cart successfully.');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $this->authorize('update', $cartItem);

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if (!$cartItem->product->hasStock($request->quantity)) {
            return back()->with('error', 'Insufficient stock available.');
        }

        $cartItem->update([
            'quantity' => $request->quantity,
            'expires_at' => now()->addMinutes(20),
        ]);

        return back()->with('success', 'Cart updated successfully.');
    }

    public function destroy(CartItem $cartItem)
    {
        $this->authorize('delete', $cartItem);

        $cartItem->delete();

        return back()->with('success', 'Item removed from cart.');
    }

    public function clear()
    {
        CartItem::active()
            ->forUser(auth()->user())
            ->delete();

        return back()->with('success', 'Cart cleared successfully.');
    }
}