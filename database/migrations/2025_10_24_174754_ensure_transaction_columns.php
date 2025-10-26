<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'transaction_code')) {
                $table->string('transaction_code')->unique()->after('id');
            }
            
            if (!Schema::hasColumn('transactions', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('status');
            }
            
            if (!Schema::hasColumn('transactions', 'payment_details')) {
                $table->text('payment_details')->nullable()->after('paid_at');
            }
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'transaction_code')) {
                $table->dropColumn('transaction_code');
            }
            if (Schema::hasColumn('transactions', 'paid_at')) {
                $table->dropColumn('paid_at');
            }
            if (Schema::hasColumn('transactions', 'payment_details')) {
                $table->dropColumn('payment_details');
            }
        });
    }
};
