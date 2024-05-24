<?php

namespace App\Filament\Resources\AyahResource\Pages;

use App\Filament\Resources\AyahResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAyahs extends ListRecords
{
    protected static string $resource = AyahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
