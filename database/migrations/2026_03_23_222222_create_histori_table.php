<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('histori_penerimaan', function (Blueprint $table) {
            $table->id();

            // Relasi ke DTKS (master data)
            $table->foreignId('dtks_id')->constrained('dtks')->onDelete('cascade');

            // Program bantuan
            $table->enum('program', ['PKH', 'BPNT', 'PBI-JK', 'ATENSI']);

            // Data penerimaan
            $table->date('tanggal_terima');
            $table->string('periode_bantuan')->nullable(); // misal: "Januari 2026"
            $table->decimal('nominal_bantuan', 15, 2)->nullable();
            $table->string('lokasi_penyerahan')->nullable();
            $table->string('petugas_penyerah')->nullable();
            $table->string('foto_bukti')->nullable();
            $table->text('catatan_penerimaan')->nullable();
            $table->enum('status_penerimaan', ['diterima', 'tidak'])->default('diterima');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('histori_penerimaan');
    }
};
