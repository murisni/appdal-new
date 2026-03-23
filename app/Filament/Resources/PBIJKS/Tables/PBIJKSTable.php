<?php

namespace App\Filament\Resources\PBIJKS\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\PBIJK;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Model;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;

class PBIJKSTable
{
    public static function getTabs(): array
    {
        $user = auth()->user();

        $baseQuery = fn() => PBIJK::whereHas('dtks', function ($q) use ($user) {
            if ($user->hasRole('user')) {
                $q->where('kecamatan', $user->kecamatan);
            }
        });

        return [
            'semua' => Tab::make('Semua Data')
                ->icon('heroicon-m-list-bullet'),

            'ditinjau' => Tab::make('Ditinjau')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'ditinjau'))
                ->badge(fn() => $baseQuery()->where('status', 'ditinjau')->count())
                ->badgeColor('gray')
                ->icon('heroicon-m-clock'),

            'diproses' => Tab::make('Sedang Disurvey')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'diproses'))
                ->badge(fn() => $baseQuery()->where('status', 'diproses')->count())
                ->badgeColor('warning')
                ->icon('heroicon-m-arrow-path'),

            'diterima' => Tab::make('Diterima')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'diterima'))
                ->badge(fn() => $baseQuery()->where('status', 'diterima')->count())
                ->badgeColor('success')
                ->icon('heroicon-m-check-circle'),

            'ditolak' => Tab::make('Ditolak')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'ditolak'))
                ->badge(fn() => $baseQuery()->where('status', 'ditolak')->count())
                ->badgeColor('danger')
                ->icon('heroicon-m-x-circle'),
        ];
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_penerima')
                    ->label('Kepala Keluarga')
                    ->searchable()
                    ->sortable()
                    ->description(fn(PBIJK $record): string => $record->dtks ? 'No. KK: ' . $record->dtks->no_kk : ''),

                TextColumn::make('nomor_bpjs')
                    ->label('No. Kartu BPJS')
                    ->searchable()
                    ->copyable()
                    ->placeholder('Belum diinput'),

                TextColumn::make('faskes_tingkat_1')
                    ->label('Faskes / Puskesmas')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Tgl Input')
                    ->dateTime('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('status_penerimaan')
                    ->label('Histori Penerima')
                    ->placeholder('Semua Data')
                    ->trueLabel('Sudah Diterima (Ada Histori)')
                    ->falseLabel('Belum Diterima Sama Sekali')
                    ->queries(
                        true: fn(Builder $query) => $query->whereNotNull('histori_penerimaan')->where('histori_penerimaan', '!=', '[]'),
                        false: fn(Builder $query) => $query->whereNull('histori_penerimaan')->orWhere('histori_penerimaan', '[]'),
                        blank: fn(Builder $query) => $query,
                    )->native(false),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->label(fn($record) => empty($record->nomor_bpjs) ? 'Tambah Data' : 'Edit Data')
                        ->icon(fn($record) => empty($record->nomor_bpjs) ? 'heroicon-m-plus-circle' : 'heroicon-m-pencil-square')
                        ->color(fn($record) => empty($record->nomor_bpjs) ? 'success' : 'gray'),

                    Action::make('lanjut_survey')
                        ->label('Lanjut Verifikasi')
                        ->icon('heroicon-m-arrow-right-circle')
                        ->color('primary')
                        ->modalSubmitAction(fn($action) => $action->color('warning'))
                        ->form([
                            Textarea::make('alasan_tinjauan_kembali')->label('⚠️ Alasan Dikembalikan')->disabled()->visible(fn($record) => filled($record->alasan_tinjauan_kembali)),
                            Textarea::make('catatan_peninjau')->label('Catatan untuk Verifikator')->placeholder('Misal: Tolong cek keaktifan kartu BPJS...')->required(),
                        ])
                        ->fillForm(fn($record) => ['alasan_tinjauan_kembali' => $record->alasan_tinjauan_kembali, 'catatan_peninjau' => $record->catatan_peninjau])
                        ->visible(function ($record) {
                            $user = auth()->user();
                            return ($user->hasRole('super_admin') || $user->can('LanjutSurveyPbijk'))
                                && $record->status === 'ditinjau' && !empty($record->nomor_bpjs);
                        })
                        ->action(function (array $data, PBIJK $record): void {
                            $record->update(['status' => 'diproses', 'catatan_peninjau' => $data['catatan_peninjau']]);
                            Notification::make()->title('Diteruskan ke Verifikator')->success()->send();
                        }),

                    Action::make('lihat_catatan')
                        ->label('Catatan Peninjau')
                        ->icon('heroicon-m-document-text')
                        ->color('info')
                        ->modalSubmitAction(false)
                        ->form([
                            Textarea::make('alasan_tinjauan_kembali')->label('⚠️ Alasan Dikembalikan')->disabled()->visible(fn($record) => filled($record->alasan_tinjauan_kembali)),
                            Textarea::make('catatan_peninjau')->label('Instruksi Peninjau Administrasi')->disabled(),
                        ])
                        ->visible(function ($record) {
                            $user = auth()->user();
                            return ($user->hasRole('super_admin') || $user->can('LihatCatatanPbijk'))
                                && $record->status === 'diproses' && $record->catatan_peninjau;
                        })
                        ->fillForm(fn($record) => ['alasan_tinjauan_kembali' => $record->alasan_tinjauan_kembali, 'catatan_peninjau' => $record->catatan_peninjau]),

                    Action::make('input_survey')
                        ->label('Input Hasil Verifikasi')
                        ->icon('heroicon-m-clipboard-document-check')
                        ->color('warning')
                        ->modalWidth('lg')
                        ->form([
                            Textarea::make('alasan_tinjauan_kembali')->label('⚠️ Alasan Minta Disurvey Ulang')->disabled()->visible(fn($record) => filled($record->alasan_tinjauan_kembali)),
                            ToggleButtons::make('status')->label('Keputusan Akhir')->options(['diterima' => 'Diterima (Aktif)', 'ditolak' => 'Ditolak (Non-Aktif)'])->colors(['diterima' => 'success', 'ditolak' => 'danger'])->inline()->required(),
                            Textarea::make('catatan_surveyor')->label('Catatan Hasil Verifikasi')->required(),
                        ])
                        ->fillForm(fn($record) => ['alasan_tinjauan_kembali' => $record->alasan_tinjauan_kembali, 'catatan_surveyor' => $record->catatan_surveyor])
                        ->visible(function ($record) {
                            $user = auth()->user();
                            return ($user->hasRole('super_admin') || $user->can('InputSurveyPbijk'))
                                && $record->status === 'diproses';
                        })
                        ->action(function (array $data, PBIJK $record): void {
                            $record->update(['status' => $data['status'], 'catatan_surveyor' => $data['catatan_surveyor'], 'alasan_tinjauan_kembali' => null]);
                            Notification::make()->title('Data Verifikasi Tersimpan')->success()->send();
                        }),

                    Action::make('tinjau_kembali')
                        ->label('Tinjau Kembali')
                        ->icon('heroicon-m-arrow-path-rounded-square')
                        ->color('secondary')
                        ->form([
                            Grid::make(2)->schema([Textarea::make('catatan_peninjau')->label('Catatan Peninjau Awal')->disabled(), Textarea::make('catatan_surveyor')->label('Catatan Lapangan Sebelumnya')->disabled(),]),
                            ToggleButtons::make('status')->options(['ditinjau' => 'Kembalikan ke Awal (Ditinjau Ulang)', 'diterima' => 'Ubah jadi Diterima', 'ditolak' => 'Ubah jadi Ditolak'])->colors(['ditinjau' => 'warning', 'diterima' => 'success', 'ditolak' => 'danger'])->inline()->required(),
                            Textarea::make('alasan_tinjauan_kembali')->label('Alasan Tinjauan Kembali')->required()->columnSpanFull(),
                        ])
                        ->fillForm(fn($record) => ['catatan_peninjau' => $record->catatan_peninjau, 'catatan_surveyor' => $record->catatan_surveyor, 'status' => $record->status, 'alasan_tinjauan_kembali' => $record->alasan_tinjauan_kembali])
                        ->visible(function ($record) {
                            $user = auth()->user();
                            return ($user->hasRole('super_admin') || $user->can('TinjauKembaliPbijk'))
                                && in_array($record->status, ['diterima', 'ditolak']);
                        })
                        ->action(function (array $data, PBIJK $record): void {
                            $record->update(['status' => $data['status'], 'alasan_tinjauan_kembali' => $data['alasan_tinjauan_kembali']]);
                            Notification::make()->title('Status Diperbarui')->success()->send();
                        }),

                    DeleteAction::make(),
                ])->label('Aksi')->icon('heroicon-m-ellipsis-vertical')->tooltip('Klik untuk melihat menu'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
