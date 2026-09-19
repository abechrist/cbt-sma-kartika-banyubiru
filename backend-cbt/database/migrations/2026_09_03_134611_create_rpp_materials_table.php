<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rpp_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rpp_id')->constrained()->onDelete('cascade');
            $table->foreignId('learning_material_id')->constrained()->onDelete('set null')->nullable();
            $table->integer('pertemuan_ke');
            $table->string('judul_topik');
            $table->text('deskripsi_aktivitas')->nullable();
            $table->json('rekomendasi_bahan_ajar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rpp_materials');
    }
};
