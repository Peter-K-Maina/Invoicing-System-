<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Add columns if they don't exist
            if (!Schema::hasColumn('invoices', 'checkout_request_id')) {
                $table->string('checkout_request_id')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'transaction_ref')) {
                $table->string('transaction_ref')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['checkout_request_id', 'transaction_ref']);
        });
    }
};
