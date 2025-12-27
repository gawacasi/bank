<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExternalTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('external_transactions', function (Blueprint $table) {
            $table->id();
            $table->dateTime('csv_created_at')->nullable();
            $table->string('type', 10);
            $table->decimal('amount', 20, 2);
            $table->foreignId('sender_wallet_id')->nullable()->constrained('wallets')->nullOnDelete();
            $table->foreignId('receiver_wallet_id')->nullable()->constrained('wallets')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('external_transactions');
    }
}
