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
    Schema::create('adoption_applications', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
      $table->foreignId('rescue_id')->nullable()->constrained('rescues')->onDelete('set null');
      $table->enum('status', ['pending','approved','rejected','under_review'])->default('pending');
      $table->text('reason_for_adoption');
      $table->date('preferred_inspection_start_date');
      $table->date('preferred_inspection_end_date');
      $table->string('valid_id');
      $table->json('supporting_documents');
      $table->string('reviewed_by')->nullable();
      $table->date('review_date')->nullable();
      $table->text('review_notes')->nullable();

      $table->timestamp('application_date')->useCurrent();
      $table->timestamp('updated_at')->nullable();
    });
  }

  /**
    * Reverse the migrations.
  */
  public function down(): void
  {
    Schema::dropIfExists('adoption_applications');
  }
};
