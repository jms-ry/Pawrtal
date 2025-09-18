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
    Schema::create('donations', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
      $table->enum('donation_type',['monetary','in-kind']);
      $table->float('amount')->nullable();
      $table->text('item_description')->nullable();
      $table->integer('item_quantity')->nullable();
      $table->enum('status',['pending','approved','picked_up','rejected','archived','cancelled']);
      $table->string('pick_up_location')->nullable();
      $table->string('contact_person')->nullable();

      $table->timestamp('donation_date')->useCurrent();
      $table->timestamp('updated_at')->nullable();
    });
  }

  /**
    * Reverse the migrations.
  */
  public function down(): void
  {
    Schema::dropIfExists('donations');
  }
};
