<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
        $table->string('checkout_request_id')->nullable()->after('status');
        $table->string('mpesa_receipt_number')->nullable()->after('checkout_request_id');
        $table->string('transaction_type')->nullable()->after('mpesa_receipt_number');
        $table->string('phone_number')->nullable()->after('transaction_type');
        $table->decimal('amount', 10, 2)->nullable()->after('phone_number');
        $table->timestamp('paid_at')->nullable()->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
        $table->dropColumn('checkout_request_id');
        $table->dropColumn('mpesa_receipt_number');
        $table->dropColumn('transaction_type');
        $table->dropColumn('phone_number');
        $table->dropColumn('amount');
        $table->dropColumn('paid_at');
    });
    }
};
