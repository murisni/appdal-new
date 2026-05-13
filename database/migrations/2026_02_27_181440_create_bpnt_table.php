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
        Schema::create('bpnt', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dtks_id')->constrained('dtks')->onDelete('cascade');
            $table->string('no_kartu_kks', 16)->nullable();
            $table->enum('status_pangan', ['terima', 'tidak'])->default('terima');
            $table->string('status')->default('ditinjau');
            $table->text('catatan_peninjau')->nullable();
            $table->text('catatan_surveyor')->nullable();
            $table->text('alasan_tinjauan_kembali')->nullable();
            $table->string('status_kpm')->default('Aktif');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bpnt');
    }
};
