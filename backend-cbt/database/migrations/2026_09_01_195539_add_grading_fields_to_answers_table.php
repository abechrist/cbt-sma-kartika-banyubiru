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
        Schema::table('answers', function (Blueprint $table) {
            $table->unsignedBigInteger('graded_by')->nullable()->after('score');
            $table->dateTime('graded_at')->nullable()->after('graded_by');
            $table->text('notes')->nullable()->after('graded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('answers', function (Blueprint $table) {
            $table->dropColumn(['graded_by', 'graded_at', 'notes']);
        });
    }
};
