<?php

namespace App\Http\Controllers;

use App\Helpers\TransactionCodeGenerator;
use App\Models\CartItem;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Snap;
use Midtrans\Transaction as MidtransTransaction;

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

            try {
                $snapToken = $this->generateSnapToken($transaction);
                if ($snapToken) {
                    $transaction->update(['snap_token' => $snapToken]);
                    \Log::info('Snap token generated successfully for transaction: ' . $transaction->transaction_code);
                } else {
                    \Log::warning('Snap token is empty for transaction: ' . $transaction->transaction_code);
                }
            } catch (\Exception $e) {
                \Log::error('Midtrans Snap Token Error: ' . $e->getMessage(), [
                    'transaction_code' => $transaction->transaction_code,
                    'exception' => $e
                ]);
            }

            return redirect()->route('checkout.success', $transaction->transaction_code)
                ->with('success', 'Pesanan berhasil dibuat. Silakan lakukan pembayaran.');
        });
    }

    private function generateSnapToken(Transaction $transaction)
    {
        try {
            $transactionDetails = [
                'order_id' => $transaction->transaction_code,
                'gross_amount' => (int) $transaction->total_price,
            ];

            $customerDetails = [
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'phone' => $transaction->phone,
                'billing_address' => [
                    'address' => $transaction->address,
                    'city' => $transaction->city,
                    'postal_code' => $transaction->postal_code,
                    'country_code' => 'IDN',
                ],
                'shipping_address' => [
                    'address' => $transaction->address,
                    'city' => $transaction->city,
                    'postal_code' => $transaction->postal_code,
                    'country_code' => 'IDN',
                ],
            ];

            $items = [];
            foreach ($transaction->items as $item) {
                $items[] = [
                    'id' => $item->product_id,
                    'price' => (int) $item->price,
                    'quantity' => $item->quantity,
                    'name' => $item->product->name,
                ];
            }

            $payload = [
                'transaction_details' => $transactionDetails,
                'customer_details' => $customerDetails,
                'item_details' => $items,
                'enabled_payments' => [
                    'qris',
                    'bank_transfer',
                    'echannel',
                    'gopay',
                    'shopeepay',
                    'dana',
                ],
            ];

            \Log::debug('Midtrans Snap Payload:', $payload);

            $snapToken = Snap::getSnapToken($payload);
            
            \Log::debug('Snap token received:', ['token' => substr($snapToken, 0, 20) . '...']);

            return $snapToken;
        } catch (\Exception $e) {
            \Log::error('Error generating snap token: ' . $e->getMessage());
            throw $e;
        }
    }

    public function success($transaction_code)
    {
        $transaction = Transaction::with('items.product')
            ->where('transaction_code', $transaction_code)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('checkout-success', compact('transaction'));
    }

    public function handleNotification(Request $request)
    {
        $payload = $request->all();
        $serverKey = config('midtrans.server_key');
        $hashed = hash('sha512', $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . $serverKey);

        if ($hashed !== $payload['signature_key']) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $transaction = Transaction::where('transaction_code', $payload['order_id'])->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $transactionStatus = $payload['transaction_status'];

        if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
            $transaction->update([
                'status' => 'paid',
                'paid_at' => now(),
                'payment_details' => $payload,
                'midtrans_transaction_id' => $payload['transaction_id'] ?? null,
            ]);
        } elseif ($transactionStatus === 'pending') {
            $transaction->update([
                'status' => 'pending',
                'payment_details' => $payload,
            ]);
        } elseif ($transactionStatus === 'deny' || $transactionStatus === 'cancel' || $transactionStatus === 'expire') {
            $transaction->update([
                'status' => 'expired',
                'payment_details' => $payload,
            ]);
        }

        return response()->json(['message' => 'Notification processed']);
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
