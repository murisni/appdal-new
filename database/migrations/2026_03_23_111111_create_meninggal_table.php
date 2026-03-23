<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meninggal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dtks_id')->constrained('dtks')->onDelete('cascade');

            // Data almarhum
            $table->string('nama_almarhum');
            $table->string('nik_almarhum', 16)->nullable();
            $table->string('status_hubungan')->default('Kepala Keluarga');
            $table->date('tanggal_meninggal');
            $table->string('bukti_meninggal')->nullable();
            $table->text('catatan')->nullable();

            // Ahli waris / pengganti
            $table->string('nama_pengganti')->nullable();
            $table->string('nik_pengganti', 16)->nullable();
            $table->string('hubungan_pengganti')->nullable();

            // Program yang terdampak
            $table->json('program_terdampak')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meninggal');
    }
};
