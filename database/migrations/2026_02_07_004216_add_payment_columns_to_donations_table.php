<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
    * Run the migrations.
  */
  public function up(): void
  {
    Schema::table('donations', function (Blueprint $table) {
      $table->string('payment_method')->nullable()->after('amount');
      $table->string('payment_intent_id')->nullable()->after('payment_method');
      $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->nullable()->after('payment_intent_id');
      $table->string('transaction_reference')->nullable()->after('payment_status');
      $table->timestamp('paid_at')->nullable()->after('transaction_reference');
    });
  }

  /**
    * Reverse the migrations.
  */
  public function down(): void
  {
    Schema::table('donations', function (Blueprint $table) {
      $table->dropColumn([
        'payment_method',
        'payment_intent_id',
        'payment_status',
        'transaction_reference',
        'paid_at'
      ]);
    });
  }
};
