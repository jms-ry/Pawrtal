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
    Schema::create('households', function (Blueprint $table) {
      $table->id();
      $table->string('house_structure');
      $table->integer('household_members');
      $table->boolean('have_children');
      $table->integer('number_of_children')->nullable();
      $table->boolean('has_other_pets');
      $table->string('current_pets')->nullable();
      $table->integer('number_of_current_pets')->nullable();
      $table->timestamps();
    });
  }

  /**
    * Reverse the migrations.
  */
  public function down(): void
  {
    Schema::dropIfExists('households');
  }
};
