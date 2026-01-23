<?php

namespace App\Filament\Resources\TherapistTypes\Pages;

use App\Filament\Resources\TherapistTypes\TherapistTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTherapistType extends EditRecord
{
    protected static string $resource = TherapistTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
