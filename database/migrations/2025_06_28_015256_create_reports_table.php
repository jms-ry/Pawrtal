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
    Schema::create('reports', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
      $table->enum('type', ['lost', 'found']);
      $table->string('species');
      $table->string('breed')->nullable();
      $table->string('color');
      $table->enum('sex',['male','female','unknown']);
      $table->string('age_estimate')->nullable();
      $table->enum('size', ['small', 'medium', 'large']);
      $table->string('found_location')->nullable();
      $table->date('found_date')->nullable();
      $table->string('distinctive_features')->nullable();
      $table->string('last_seen_location')->nullable();
      $table->date('last_seen_date')->nullable();
      $table->string('condition')->nullable();
      $table->string('temporary_shelter')->nullable();
      $table->string('image');
      $table->enum('status', ['active', 'resolved'])->default('active');

      $table->timestamps();
    });
  }

  /**
    * Reverse the migrations.
  */
  public function down(): void
  {
     Schema::dropIfExists('reports');
  }
};
