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
    Schema::create('inspection_schedules', function (Blueprint $table) {
      $table->id();
      $table->foreignId('application_id')->nullable()->constrained('adoption_applications')->onDelete('set null');
      $table->foreignId('inspector_id')->nullable()->constrained('users')->onDelete('set null');
      $table->date('inspection_date');
      $table->string('inspection_location')->nullable();
      $table->timestamps();
    });
  }

  /**
    * Reverse the migrations.
  */
  public function down(): void
  {
    Schema::dropIfExists('inspection_schedules');
  }
};
