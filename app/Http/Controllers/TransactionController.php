<?php

namespace App\Http\Controllers;

use App\Helpers\TransactionCodeGenerator;
use App\Models\CartItem;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function showCheckoutForm()
    {
        $cartItems = CartItem::with('product')
            ->active()
            ->forUser(auth()->user())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda kosong.');
        }

        $total = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        $paymentMethods = [
            'qris' => [
                'name' => 'QRIS',
                'description' => 'Scan QR Code untuk pembayaran',
                'logo' => 'QRIS'
            ],
            'bni_va' => [
                'name' => 'BNI Virtual Account',
                'description' => 'Minimum Pembayaran Sebesar Rp. 10.000,00',
                'logo' => 'BNI'
            ],
            'mandiri_va' => [
                'name' => 'Mandiri Virtual Account',
                'description' => 'Minimum Pembayaran Sebesar Rp. 10.000,00',
                'logo' => 'Mandiri'
            ],
            'bri_va' => [
                'name' => 'BRI Virtual Account',
                'description' => 'Minimum Pembayaran Sebesar Rp. 10.000,00',
                'logo' => 'BRI'
            ],
            'qr_dana' => [
                'name' => 'QR Dana',
                'description' => 'Scan QR Code untuk pembayaran',
                'logo' => 'Dana'
            ],
            'qr_gopay' => [
                'name' => 'QR Go-Pay',
                'description' => 'Scan QR Code untuk pembayaran',
                'logo' => 'GoPay'
            ],
            'qr_shopeepay' => [
                'name' => 'QR Shopee Pay',
                'description' => 'Scan QR Code untuk pembayaran',
                'logo' => 'ShopeePay'
            ],
            'qr_ovo' => [
                'name' => 'QR OVO',
                'description' => 'Scan QR Code untuk pembayaran',
                'logo' => 'OVO'
            ],
        ];

        return view('checkout-form', compact('cartItems', 'total', 'paymentMethods'));
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'city' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'payment_method' => 'required|string',
        ]);

        $cartItems = CartItem::with('product')
            ->active()
            ->forUser(auth()->user())
            ->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Keranjang Anda kosong.');
        }

        foreach ($cartItems as $item) {
            if (!$item->product->hasStock($item->quantity)) {
                return back()->with('error', "Stok tidak cukup untuk {$item->product->name}.");
            }
        }

        return DB::transaction(function () use ($cartItems, $validated) {
            $total = $cartItems->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });

            $transactionCode = TransactionCodeGenerator::generate();

            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'transaction_code' => $transactionCode,
                'status' => 'pending',
                'total_price' => $total,
                'expires_at' => now()->addHours(24),
                'address' => $validated['address'],
                'postal_code' => $validated['postal_code'],
                'city' => $validated['city'],
                'phone' => $validated['phone'],
                'payment_method' => $validated['payment_method'],
            ]);

            foreach ($cartItems as $item) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);

                $item->product->decreaseStock($item->quantity);
            }

            $cartItems->each->delete();

            return redirect()->route('checkout.success', $transaction->transaction_code)
                ->with('success', 'Pesanan berhasil dibuat. Silakan lakukan pembayaran.');
        });
    }

    public function success($transaction_code)
    {
        $transaction = Transaction::with('items.product')
            ->where('transaction_code', $transaction_code)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('checkout-success', compact('transaction'));
    }

    public function history()
    {
        $transactions = Transaction::with('items.product')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('transactions.history', compact('transactions'));
    }
}
