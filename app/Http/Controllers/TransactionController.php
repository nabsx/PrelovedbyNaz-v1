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
                'midtrans_order_id' => $transactionCode,
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

    public function testNotification($transaction_code)
    {
        $transaction = Transaction::where('transaction_code', $transaction_code)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $grossAmount = (int) $transaction->total_price;
        
        // Simulasi notifikasi dari Midtrans
        $payload = [
            'order_id' => $transaction->transaction_code,
            'transaction_status' => 'settlement',
            'status_code' => '200',
            'gross_amount' => $grossAmount,
            'transaction_id' => 'midtrans-' . time(),
            'signature_key' => hash('sha512', $transaction->transaction_code . '|200|' . $grossAmount . '|' . config('midtrans.server_key')),
        ];

        \Log::info('=== MANUAL TEST NOTIFICATION ===', $payload);

        // Call handleNotification dengan payload test
        $request = new Request($payload);
        return $this->handleNotification($request);
    }

    public function getTransactionStatus($transaction_code)
    {
        $transaction = Transaction::where('transaction_code', $transaction_code)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return response()->json([
            'transaction_code' => $transaction->transaction_code,
            'status' => $transaction->status,
            'paid_at' => $transaction->paid_at,
            'total_price' => $transaction->total_price,
            'payment_details' => $transaction->payment_details,
            'created_at' => $transaction->created_at,
        ]);
    }

    public function checkStatus($transaction_code)
    {
        $transaction = Transaction::where('transaction_code', $transaction_code)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return response()->json([
            'status' => $transaction->status,
            'paid_at' => $transaction->paid_at,
            'is_paid' => $transaction->isPaid(),
        ]);
    }

    public function handleNotification(Request $request)
    {
        \Log::info('=== MIDTRANS NOTIFICATION RECEIVED ===', [
            'method' => $request->method(),
            'path' => $request->path(),
            'headers' => $request->headers->all(),
            'all_data' => $request->all(),
        ]);

        $payload = $request->all();
        
        \Log::info('=== MIDTRANS NOTIFICATION PAYLOAD ===', [
            'order_id' => $payload['order_id'] ?? null,
            'transaction_status' => $payload['transaction_status'] ?? null,
            'status_code' => $payload['status_code'] ?? null,
            'gross_amount' => $payload['gross_amount'] ?? null,
        ]);
        
        $orderId = $payload['order_id'] ?? '';
        
        if (!$orderId) {
            \Log::error('ORDER ID NOT FOUND IN NOTIFICATION');
            return response()->json(['message' => 'Order ID not found'], 200);
        }

        try {
            $midtransStatus = MidtransTransaction::status($orderId);
            
            \Log::info('Midtrans Status Retrieved', [
                'order_id' => $orderId,
                'transaction_status' => $midtransStatus->transaction_status ?? null,
                'status_code' => $midtransStatus->status_code ?? null,
            ]);
            
            $transactionStatus = $midtransStatus->transaction_status ?? '';
        } catch (\Exception $e) {
            \Log::error('Error getting Midtrans status: ' . $e->getMessage());
            // Fallback to payload status if SDK call fails
            $transactionStatus = $payload['transaction_status'] ?? '';
        }

        $transaction = Transaction::where('transaction_code', $orderId)->first();

        if (!$transaction) {
            \Log::warning('TRANSACTION NOT FOUND', ['order_id' => $orderId]);
            return response()->json(['message' => 'Transaction not found'], 200);
        }

        \Log::info('Processing Transaction Status', [
            'transaction_code' => $transaction->transaction_code,
            'current_status' => $transaction->status,
            'new_status' => $transactionStatus,
        ]);

        if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
            $transaction->update([
                'status' => 'paid',
                'paid_at' => now(),
                'payment_details' => json_encode($payload),
                'midtrans_transaction_id' => $payload['transaction_id'] ?? null,
            ]);
            \Log::info('✓ TRANSACTION MARKED AS PAID', [
                'transaction_code' => $transaction->transaction_code,
                'paid_at' => $transaction->paid_at,
                'status' => $transaction->status,
            ]);
        } elseif ($transactionStatus === 'pending') {
            $transaction->update([
                'status' => 'pending',
                'payment_details' => json_encode($payload),
            ]);
            \Log::info('Transaction Status: PENDING', ['transaction_code' => $transaction->transaction_code]);
        } elseif ($transactionStatus === 'deny' || $transactionStatus === 'cancel' || $transactionStatus === 'expire') {
            $transaction->update([
                'status' => 'expired',
                'payment_details' => json_encode($payload),
            ]);
            \Log::info('Transaction Status: EXPIRED', ['transaction_code' => $transaction->transaction_code]);
        }

        \Log::info('=== NOTIFICATION PROCESSED SUCCESSFULLY ===', [
            'transaction_code' => $transaction->transaction_code,
            'final_status' => $transaction->status,
        ]);
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
