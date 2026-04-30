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
        Schema::create('sponsors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hackathon_id')->constrained('hackathons')->cascadeOnDelete();
            $table->string('name');
            $table->string('logo');
            $table->string('url')->nullable();
            $table->enum('tier', ['title', 'gold', 'silver', 'bronze'])->default('bronze');
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();

            $table->index('hackathon_id');
            $table->index('tier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sponsors');
    }
};
