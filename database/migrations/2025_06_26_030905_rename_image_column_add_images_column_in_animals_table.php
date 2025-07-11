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
    Schema::table('animals', function (Blueprint $table) {
      $table->renameColumn('image', 'profile_image');
      $table->renameColumn('destinctive_features', 'distinctive_features');
      $table->json('images')->nullable();
    });
  }

  /**
    * Reverse the migrations.
  */
  public function down(): void
  {
    Schema::table('animals', function (Blueprint $table) {
      $table->renameColumn('profile_image', 'image');
      $table->renameColumn('distinctive_features', 'destinctive_features');
      $table->dropColumn('images');
    });
  }
};
