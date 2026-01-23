<?php

namespace App\Filament\Resources\TherapistTypes\Pages;

use App\Filament\Resources\TherapistTypes\TherapistTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTherapistTypes extends ListRecords
{
    protected static string $resource = TherapistTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
