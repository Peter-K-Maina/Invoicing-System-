<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'checkout_request_id')) {
                $table->string('checkout_request_id')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'transaction_ref')) {
                $table->string('transaction_ref')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'mpesa_receipt')) {
                $table->string('mpesa_receipt')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'paid_amount')) {
                $table->decimal('paid_amount', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('invoices', 'payer_phone')) {
                $table->string('payer_phone')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'paid_at')) {
                $table->timestamp('paid_at')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $columns = [
                'checkout_request_id',
                'transaction_ref',
                'mpesa_receipt',
                'paid_amount',
                'payer_phone',
                'paid_at'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('invoices', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
