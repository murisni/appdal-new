<?php

namespace App\Filament\Resources\KepalaDinas\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class KepalaDinasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pejabat')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('nip')
                                ->label('NIP')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(50),
                            TextInput::make('nama_lengkap')
                                ->label('Nama Lengkap (Beserta Gelar)')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('pangkat_golongan')
                                ->label('Pangkat / Golongan')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('jabatan')
                                ->label('Jabatan')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('periode_jabatan')
                                ->label('Periode Jabatan')
                                ->required()
                                ->maxLength(255),
                            Select::make('status_pejabat')
                                ->label('Status Pejabat')
                                ->options([
                                    'Definitif' => 'Definitif',
                                    'Plt' => 'Plt (Pelaksana Tugas)',
                                    'Plh' => 'Plh (Pelaksana Harian)',
                                ])
                                ->required()
                                ->default('Definitif'),
                        ]),
                    ]),
                Section::make('Tanda Tangan & Status Aktif')
                    ->schema([
                        FileUpload::make('foto_ttd')
                            ->label('Upload Tanda Tangan')
                            ->image()
                            ->directory('tanda-tangan')
                            ->disk('public')
                            ->required(),
                        Toggle::make('is_active')
                            ->label('Jadikan Kepala Dinas Aktif Saat Ini')
                            ->default(false)
                            ->inline(false),
                    ]),
            ]);
    }
}
