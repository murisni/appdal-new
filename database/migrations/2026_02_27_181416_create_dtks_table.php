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
        Schema::create('dtks', function (Blueprint $table) {
            $table->id();
            $table->string('no_kk', 16)->index();
            $table->text('alamat');
            $table->string('rt', 5);
            $table->string('rw', 5);
            $table->string('provinsi')->default('Kalimantan Tengah');
            $table->string('kabupaten')->default('Kapuas');
            $table->string('kecamatan');
            $table->string('kelurahan');
            $table->json('anggota_keluarga')->nullable();
            $table->string('detail_pekerjaan')->nullable();
            $table->string('nama_tempat_kerja')->nullable();
            $table->decimal('penghasilan_per_bulan', 15, 2)->default(0);
            $table->decimal('penghasilan_lainnya', 15, 2)->default(0);
            $table->decimal('pengeluaran_per_bulan', 15, 2)->default(0);
            $table->string('jumlah_tanggungan', 5)->default('0');
            $table->string('status_kepemilikan_rumah')->nullable();
            $table->string('daya_listrik', 10)->nullable();
            $table->string('jenis_lantai')->nullable();
            $table->string('sumber_air')->nullable();
            $table->boolean('ada_lansia_disabilitas')->default(false);
            $table->string('aset_lainnya')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('file_kk')->nullable();
            $table->string('file_sktm')->nullable();
            $table->string('foto_rumah_depan')->nullable();
            $table->string('foto_rumah_tamu')->nullable();
            $table->string('foto_rumah_dapur')->nullable();
            $table->string('status')->default('ditinjau');
            $table->text('catatan_peninjau')->nullable();
            $table->text('catatan_surveyor')->nullable();
            $table->text('alasan_tinjauan_kembali')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->string('status_kpm')->default('Aktif');
            $table->date('tanggal_meninggal')->nullable();
            $table->string('bukti_meninggal')->nullable();
            $table->string('nama_pengganti')->nullable();
            $table->string('nik_pengganti')->nullable();
            $table->string('hubungan_pengganti')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dtks');
    }
};
