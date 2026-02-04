<?php

namespace App\Filament\Resources\DailyRoomRecords\Pages;

use App\Filament\Resources\DailyRoomRecords\DailyRoomRecordResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDailyRoomRecord extends CreateRecord
{
    protected static string $resource = DailyRoomRecordResource::class;

    protected function getRedirectUrl(): string
    {
        // Stays on the create page so you can add another record immediately
        return $this->getResource()::getUrl('index');
    }
}
