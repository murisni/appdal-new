<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">Filter Data Laporan Histori</x-slot>
        <x-slot name="description">Pilih program bantuan untuk melihat histori penyaluran bukti foto dan catatan.</x-slot>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
            {{-- 1. Tipe Laporan --}}
            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium leading-none text-gray-950 dark:text-white">Tipe Waktu Laporan</label>
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model.live="tipe_laporan">
                        <option value="harian">Harian</option>
                        <option value="bulanan">Bulanan</option>
                        <option value="triwulan">Triwulan</option>
                        <option value="tahunan">Tahunan</option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>

            {{-- 2. Input Dinamis Waktu --}}
            @if($tipe_laporan === 'harian')
            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium leading-none text-gray-950 dark:text-white">Tanggal Mulai</label>
                <x-filament::input.wrapper><x-filament::input type="date" wire:model.live="tanggal_mulai" /></x-filament::input.wrapper>
            </div>
            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium leading-none text-gray-950 dark:text-white">Tanggal Sampai</label>
                <x-filament::input.wrapper><x-filament::input type="date" wire:model.live="tanggal_sampai" /></x-filament::input.wrapper>
            </div>
            @endif

            @if(in_array($tipe_laporan, ['bulanan']))
            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium leading-none text-gray-950 dark:text-white">Bulan</label>
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model.live="bulan">
                        <option value="01">Januari</option><option value="02">Februari</option><option value="03">Maret</option>
                        <option value="04">April</option><option value="05">Mei</option><option value="06">Juni</option>
                        <option value="07">Juli</option><option value="08">Agustus</option><option value="09">September</option>
                        <option value="10">Oktober</option><option value="11">November</option><option value="12">Desember</option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>
            @endif

            @if($tipe_laporan === 'triwulan')
            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium leading-none text-gray-950 dark:text-white">Triwulan</label>
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model.live="triwulan">
                        <option value="1">Triwulan I (Jan-Mar)</option><option value="2">Triwulan II (Apr-Jun)</option>
                        <option value="3">Triwulan III (Jul-Sep)</option><option value="4">Triwulan IV (Okt-Des)</option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>
            @endif

            @if(in_array($tipe_laporan, ['bulanan', 'triwulan', 'tahunan']))
            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium leading-none text-gray-950 dark:text-white">Tahun</label>
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model.live="tahun">
                        @foreach($listTahun as $t) <option value="{{ $t }}">{{ $t }}</option> @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>
            @endif

            {{-- 3. Filter Program (Disesuaikan dengan tabel HistoriPenerimaan) --}}
            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium leading-none text-gray-950 dark:text-white text-primary-600">Program Bantuan</label>
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model.live="program">
                        <option value="semua">Semua Program</option>
                        <option value="PKH">PKH</option>
                        <option value="BPNT">BPNT</option>
                        <option value="PBI-JK">PBI-JK</option>
                        <option value="ATENSI">ATENSI</option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>
        </div>
    </x-filament::section>

    <div wire:loading class="w-full p-4 text-center">
        <x-filament::loading-indicator class="w-6 h-6 mx-auto text-primary-600" />
        <span class="text-sm text-gray-500">Memuat data histori...</span>
    </div>

    <div wire:loading.remove>
        {{ $this->table }}
    </div>
</x-filament-panels::page>