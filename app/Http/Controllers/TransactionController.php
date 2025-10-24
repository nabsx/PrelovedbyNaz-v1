<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;

class TransactionController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');
    }

    public function checkout(Request $request)
    {
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

        return DB::transaction(function () use ($cartItems) {
            $total = $cartItems->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });

            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'midtrans_order_id' => 'PRELOVED-' . now()->timestamp . '-' . auth()->id(),
                'status' => 'pending',
                'total_price' => $total,
                'expires_at' => now()->addHour(),
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
                ],
            ];

            $snapToken = Snap::getSnapToken($params);
            $transaction->update(['snap_token' => $snapToken]);

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