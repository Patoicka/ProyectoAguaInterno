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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('street')->nullable();
            $table->string('interior_number')->nullable();
            $table->string('exterior_number')->nullable();
            $table->string('additional')->nullable();
            $table->string('references', 100)->nullable();

            $table->unsignedBigInteger('neighborhood_id')->nullable();
            $table->foreign('neighborhood_id')->references("id")->on('neighborhoods')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
