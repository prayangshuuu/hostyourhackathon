<?php

use App\Enums\SponsorTier;
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
            $table->string('tier')->default(SponsorTier::Bronze->value);
            $table->timestamps();
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
