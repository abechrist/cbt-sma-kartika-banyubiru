<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rpp_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rpp_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_id')->constrained()->onDelete('set null')->nullable();
            $table->string('tp_id');
            $table->text('deskripsi_tp');
            $table->integer('jumlah_soal_direkomendasikan')->default(5);
            $table->string('tipe_soal');
            $table->string('tingkat_kesulitan');
            $table->text('kata_kunci_indokator_soal')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rpp_assessments');
    }
};
