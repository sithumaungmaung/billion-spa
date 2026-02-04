<?php

namespace App\Filament\Resources\DailyRoomRecords\Pages;

use App\Filament\Resources\DailyRoomRecords\DailyRoomRecordResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDailyRoomRecord extends EditRecord
{
    protected static string $resource = DailyRoomRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
