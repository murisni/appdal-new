<?php

namespace App\Filament\Resources\BPNTS\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ToggleButtons;
use App\Models\DTKS;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\Textarea;

class BPNTForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Formulir BPNT')
                    ->tabs([
                        Tab::make('Data KPM')
                            ->icon('heroicon-m-users')
                            ->schema([
                                Select::make('dtks_id')
                                    ->label('Pilih Keluarga (No. KK / Nama DTKS)')
                                    ->relationship(
                                        name: 'dtks',
                                        titleAttribute: 'no_kk',
                                        modifyQueryUsing: fn(Builder $query) => $query->where('status', 'diterima')
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->helperText('Hanya menampilkan data DTKS yang statusnya Diterima / Layak.'),
                            ]),

                        Tab::make('Detail BPNT')
                            ->icon('heroicon-m-credit-card')
                            ->schema([
                                TextInput::make('no_kartu_kks')
                                    ->label('Nomor Kartu KKS (Kartu Keluarga Sejahtera)')
                                    ->numeric()
                                    ->maxLength(16)
                                    ->placeholder('Masukkan 16 digit nomor kartu KKS...'),

                                ToggleButtons::make('status_pangan')
                                    ->label('Status Penerimaan Sembako')
                                    ->options([
                                        'terima' => 'Aktif Menerima',
                                        'tidak' => 'Tidak Menerima / Saldo Kosong',
                                    ])
                                    ->colors([
                                        'terima' => 'success',
                                        'tidak' => 'danger',
                                    ])
                                    ->inline()
                                    ->default('Tidak Menerima / Saldo Kosong')
                                    ->required(),
                            ])->columns(2),

                        Tab::make('Verifikasi Survey')
                            ->icon('heroicon-m-check-badge')
                            ->schema([
                                ToggleButtons::make('status')
                                    ->label('Status Kelayakan BPNT')
                                    ->options([
                                        'ditinjau' => 'Ditinjau',
                                        'diterima' => 'Diterima',
                                        'ditolak'  => 'Ditolak',
                                    ])
                                    ->colors([
                                        'ditinjau' => 'warning',
                                        'diterima' => 'success',
                                        'ditolak'  => 'danger',
                                    ])
                                    ->icons([
                                        'ditinjau' => 'heroicon-m-clock',
                                        'diterima' => 'heroicon-m-check-circle',
                                        'ditolak'  => 'heroicon-m-x-circle',
                                    ])
                                    ->inline()
                                    ->default('ditinjau')
                                    ->required(),

                                Textarea::make('catatan_surveyor')
                                    ->label('Catatan Surveyor')
                                    ->placeholder('Misal: KPM menolak karena sudah mampu, atau KKS hilang...')
                                    ->columnSpanFull()
                                    ->rows(3),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
