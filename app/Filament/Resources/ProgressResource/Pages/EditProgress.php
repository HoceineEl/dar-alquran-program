<?php

namespace App\Filament\Resources\ProgressResource\Pages;

use App\Filament\Resources\ProgressResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProgress extends EditRecord
{
    protected static string $resource = ProgressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}
