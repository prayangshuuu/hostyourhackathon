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
        Schema::table('hackathons', function (Blueprint $table) {
            $table->boolean('re_open_submission')->default(false)->after('submission_closes_at');
        });

        Schema::table('submission_files', function (Blueprint $table) {
            $table->unsignedBigInteger('file_size')->default(0)->after('original_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hackathons', function (Blueprint $table) {
            $table->dropColumn('re_open_submission');
        });

        Schema::table('submission_files', function (Blueprint $table) {
            $table->dropColumn('file_size');
        });
    }
};
