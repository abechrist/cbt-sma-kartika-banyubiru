<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nisn')->nullable()->unique()->after('email');
            $table->string('nip')->nullable()->unique()->after('nisn');
            $table->string('phone')->nullable()->after('nip');
            $table->date('birth_date')->nullable()->after('phone');
            $table->text('address')->nullable()->after('birth_date');
            $table->enum('gender', ['L', 'P'])->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['nisn']);
            $table->dropUnique(['nip']);
            $table->dropColumn(['nisn', 'nip', 'phone', 'birth_date', 'address', 'gender']);
        });
    }
};
