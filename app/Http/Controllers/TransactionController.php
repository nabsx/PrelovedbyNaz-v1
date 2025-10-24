<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    // Midtrans will be initialized only when needed (in checkout method)

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
        try {
            \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
            \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
            \Midtrans\Config::$isSanitized = config('services.midtrans.is_sanitized');
            \Midtrans\Config::$is3ds = config('services.midtrans.is_3ds');
        } catch (\Exception $e) {
            return back()->with('error', 'Payment gateway configuration error. Please contact support.');
        }

        $validated = $request->validate([
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'city' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'payment_method' => 'required|in:credit_card,bank_transfer,e_wallet',
        ]);

        $cartItems = CartItem::with('product')
            ->active()
            ->forUser(auth()->user())
            ->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Your cart is empty.');
        }

        foreach ($cartItems as $item) {
            if (!$item->product->hasStock($item->quantity)) {
                return back()->with('error', "Insufficient stock for {$item->product->name}.");
            }
        }

        return DB::transaction(function () use ($cartItems, $validated) {
            $total = $cartItems->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });

            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'midtrans_order_id' => 'PRELOVED-' . now()->timestamp . '-' . auth()->id(),
                'status' => 'pending',
                'total_price' => $total,
                'expires_at' => now()->addHour(),
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

            $params = [
                'transaction_details' => [
                    'order_id' => $transaction->midtrans_order_id,
                    'gross_amount' => (int) $total,
                ],
                'customer_details' => [
                    'first_name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                    'phone' => $validated['phone'],
                ],
            ];

            try {
                $snapToken = \Midtrans\Snap::getSnapToken($params);
                $transaction->update(['snap_token' => $snapToken]);
            } catch (\Exception $e) {
                return back()->with('error', 'Failed to generate payment token. Please try again.');
            }

            return view('checkout', compact('transaction', 'snapToken'));
        });
    }

    public function handleNotification(Request $request)
    {
        $payload = $request->all();

        $transaction = Transaction::where('midtrans_order_id', $payload['order_id'])->first();

        if (!$transaction) {
            return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
        }

        $transactionStatus = $payload['transaction_status'];
        $fraudStatus = $payload['fraud_status'];

        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'accept') {
                $transaction->update([
                    'status' => 'paid',
                    'midtrans_transaction_id' => $payload['transaction_id'],
                ]);
            }
        } elseif ($transactionStatus == 'settlement') {
            $transaction->update([
                'status' => 'paid',
                'midtrans_transaction_id' => $payload['transaction_id'],
            ]);
        } elseif ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
            $transaction->update(['status' => 'expired']);
            
            foreach ($transaction->items as $item) {
                $item->product->increaseStock($item->quantity);
            }
        }

        return response()->json(['status' => 'success']);
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
