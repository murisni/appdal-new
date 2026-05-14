<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kepala_dinas', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 50)->unique();
            $table->string('nama_lengkap');
            $table->string('pangkat_golongan');
            $table->string('jabatan');
            $table->string('periode_jabatan');
            $table->enum('status_pejabat', ['Definitif', 'Plt', 'Plh'])->default('Definitif');
            $table->boolean('is_active')->default(false);
            $table->string('foto_ttd')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kepala_dinas');
    }
};
