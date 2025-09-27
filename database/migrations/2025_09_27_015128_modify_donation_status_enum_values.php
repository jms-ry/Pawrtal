<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
  /**
    * Run the migrations.
  */
  public function up(): void
  {
    Schema::table('donations', function (Blueprint $table) {
      DB::statement("ALTER TABLE donations DROP CONSTRAINT donations_status_check");
      DB::statement("ALTER TABLE donations ADD CONSTRAINT donations_status_check CHECK (status IN ('pending','rejected','cancelled','archived','accepted'))");
    });
  }

  /**
    * Reverse the migrations.
  */
  public function down(): void
  {
    Schema::table('donations', function (Blueprint $table) {
      DB::statement("ALTER TABLE donations DROP CONSTRAINT donations_status_check");
      DB::statement("ALTER TABLE donations ADD CONSTRAINT donations_status_check CHECK (status IN ('pending','approved','rejected','cancelled','archived','picked_up'))");
    });
  }
};
