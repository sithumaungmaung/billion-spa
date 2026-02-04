<?php

namespace App\Filament\Resources\DailyRoomRecords\Pages;

use App\Filament\Resources\DailyRoomRecords\DailyRoomRecordResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDailyRoomRecords extends ListRecords
{
    protected static string $resource = DailyRoomRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
