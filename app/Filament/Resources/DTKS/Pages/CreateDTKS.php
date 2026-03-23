<?php

namespace App\Filament\Resources\DTKS\Pages;

use App\Filament\Resources\DTKS\DTKSResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDTKS extends CreateRecord
{
    protected static string $resource = DTKSResource::class;
    protected static ?string $title = 'Tambah Data DTKS';
}
