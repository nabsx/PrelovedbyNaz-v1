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
        CartItem::where('expires_at', '<=', now())->delete();

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
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi. Stok tersedia: ' . $product->stock . ' unit'
                ], 422);
            }
            return back()->with('error', 'Stok tidak mencukupi. Stok tersedia: ' . $product->stock . ' unit');
        }

        try {
            DB::transaction(function () use ($request, $product) {
                $cartItem = CartItem::active()
                    ->forUser(auth()->user())
                    ->where('product_id', $product->id)
                    ->first();

                if ($cartItem) {
                    $newQuantity = $cartItem->quantity + $request->quantity;
                    
                    if (!$product->hasStock($newQuantity)) {
                        throw new \Exception('Stok tidak mencukupi. Stok tersedia: ' . $product->stock . ' unit. Anda sudah menambahkan ' . $cartItem->quantity . ' unit.');
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

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Produk berhasil ditambahkan ke keranjang'
                ], 201);
            }

            return redirect()->route('cart.index')->with('success', 'Produk berhasil ditambahkan ke keranjang');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function updateQuantityAjax(Request $request, CartItem $cartItem)
    {
        if (auth()->check()) {
            if ($cartItem->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action.'
                ], 403);
            }
        } else {
            if ($cartItem->session_id !== session()->getId()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action.'
                ], 403);
            }
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if (!$cartItem->product->hasStock($request->quantity)) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak mencukupi. Stok tersedia: ' . $cartItem->product->stock . ' unit'
            ], 422);
        }

        try {
            $cartItem->update([
                'quantity' => $request->quantity,
                'expires_at' => now()->addMinutes(20),
            ]);

            $itemTotal = $cartItem->product->price * $cartItem->quantity;
            
            $total = CartItem::active()
                ->forUser(auth()->user())
                ->with('product')
                ->get()
                ->sum(function ($item) {
                    return $item->product->price * $item->quantity;
                });

            return response()->json([
                'success' => true,
                'message' => 'Keranjang berhasil diupdate',
                'quantity' => $cartItem->quantity,
                'itemTotal' => $itemTotal,
                'total' => $total,
                'formattedItemTotal' => 'Rp ' . number_format($itemTotal, 0, ',', '.'),
                'formattedTotal' => 'Rp ' . number_format($total, 0, ',', '.')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, CartItem $cartItem)
    {
        if (auth()->check()) {
            if ($cartItem->user_id !== auth()->id()) {
                abort(403, 'Unauthorized action.');
            }
        } else {
            if ($cartItem->session_id !== session()->getId()) {
                abort(403, 'Unauthorized action.');
            }
        }

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
        if (auth()->check()) {
            if ($cartItem->user_id !== auth()->id()) {
                abort(403, 'Unauthorized action.');
            }
        } else {
            if ($cartItem->session_id !== session()->getId()) {
                abort(403, 'Unauthorized action.');
            }
        }

        $cartItem->delete();

        return back()->with('success', 'Item removed from cart.');
    }

    public function clear()
    {
        if (auth()->check()) {
            CartItem::active()
                ->where('user_id', auth()->id())
                ->delete();
        } else {
            CartItem::active()
                ->where('session_id', session()->getId())
                ->delete();
        }

        return back()->with('success', 'Cart cleared successfully.');
    }
}
