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
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('species');
            $table->string('breed')->nullable();
            $table->text('description');
            $table->enum('sex', ['male', 'female']);
            $table->string('age')->nullable();
            $table->enum('size', ['small', 'medium', 'large'])->nullable();
            $table->string('color')->nullable();
            $table->string('destinctive_features')->nullable();
            $table->enum('health_status', ['healthy', 'sick', 'injured'])->default('healthy');
            $table->enum('vaccination_status', ['vaccinated', 'not_vaccinated','partially_vaccinated']);
            $table->boolean('spayed_neutered');
            $table->enum('adoption_status', ['available', 'adopted', 'unavailable']);
            $table->string('image');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animals');
    }
};
