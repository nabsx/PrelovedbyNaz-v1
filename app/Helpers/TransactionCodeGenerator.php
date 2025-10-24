<?php

namespace App\Helpers;

use App\Models\Transaction;

class TransactionCodeGenerator
{
    /**
     * Generate unique transaction code
     * Format: TRX-YYYYMMDD-XXXXX (e.g., TRX-20251024-A1B2C)
     */
    public static function generate(): string
    {
        $date = now()->format('Ymd');
        $randomCode = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5));
        $code = "TRX-{$date}-{$randomCode}";

        // Ensure uniqueness
        while (Transaction::where('transaction_code', $code)->exists()) {
            $randomCode = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5));
            $code = "TRX-{$date}-{$randomCode}";
        }

        return $code;
    }

    /**
     * Generate Midtrans order ID
     * Format: ORD-USERID-TIMESTAMP (e.g., ORD-5-1729777200)
     */
    public static function generateMidtransOrderId(int $userId): string
    {
        return "ORD-{$userId}-" . now()->timestamp;
    }
}
