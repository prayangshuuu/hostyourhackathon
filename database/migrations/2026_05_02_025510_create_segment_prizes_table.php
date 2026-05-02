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
        Schema::create('segment_prizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('segment_id')->constrained('segments')->cascadeOnDelete();
            $table->string('rank');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('amount')->nullable();
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();

            $table->index('segment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('segment_prizes');
    }
};
