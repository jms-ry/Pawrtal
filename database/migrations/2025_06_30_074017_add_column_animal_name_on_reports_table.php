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
    Schema::table('reports', function (Blueprint $table) {
      $table->string('animal_name')->nullable()->after('type');
    });
  }

  /**
    * Reverse the migrations.
  */
  public function down(): void
  {
    Schema::table('reports', function (Blueprint $table) {
      $table->dropColumn('animal_name');
    });
  }
};
