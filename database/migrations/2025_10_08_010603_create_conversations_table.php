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
    Schema::create('conversations', function (Blueprint $table) {
      $table->id();
      $table->foreignId('participant1_id')->constrained('users')->onDelete('cascade');
      $table->foreignId('participant2_id')->constrained('users')->onDelete('cascade');
      $table->timestamp('last_message_at')->nullable();
      $table->timestamps();

      $table->unique(['participant1_id', 'participant2_id']);

      $table->index('participant1_id');
      $table->index('participant2_id');
      $table->index('last_message_at');
    });
  }

  /**
    * Reverse the migrations.
  */
  public function down(): void
  {
    Schema::dropIfExists('conversations');
  }
};
