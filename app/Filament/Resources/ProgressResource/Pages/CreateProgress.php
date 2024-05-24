<?php

namespace App\Filament\Resources\ProgressResource\Pages;

use App\Filament\Resources\ProgressResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProgress extends CreateRecord
{
    protected static string $resource = ProgressResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        $data['created_by'] = auth()->id();

        return static::getModel()::create($data);
    }
}
