<?php

namespace App\Filament\Widgets;

use App\Models\Room;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Therapist;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Branches', Branch::count())
                ->description('Total branches')
                ->icon('heroicon-o-building-library'),

            Stat::make('Rooms', Room::count())
                ->description('Available rooms')
                ->icon('heroicon-o-building-office'),

            Stat::make('Products', Product::count())
                ->description('Total products')
                ->icon('heroicon-o-cube'),


            Stat::make('Therapists', Therapist::count()) 
                ->description('Active therapists')
                ->icon('heroicon-o-user'),

            //  Stat::make('Branches', Branch::count())
            //     ->description('32k increase')
            //     ->descriptionIcon('heroicon-m-arrow-trending-up')
            //     ->chart([7, 2, 10, 3, 15, 4, 17])
            //     ->color('success'),
        ];
    }
}
