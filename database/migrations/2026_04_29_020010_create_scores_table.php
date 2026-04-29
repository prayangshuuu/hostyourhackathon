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
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('judge_id')->constrained('judges')->cascadeOnDelete();
            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->foreignId('criteria_id')->constrained('scoring_criteria')->cascadeOnDelete();
            $table->unsignedSmallInteger('score');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['judge_id', 'submission_id', 'criteria_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};
