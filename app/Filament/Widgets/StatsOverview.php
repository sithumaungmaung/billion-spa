<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Branches\BranchResource;
use App\Filament\Resources\Rooms\RoomResource;
use App\Models\Branch;
use App\Models\DailyRoomRecord;
use App\Models\Product;
use App\Models\Room;
use App\Models\Therapist;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $branchId = session('branch_id');

        return [
            Stat::make('Branches', Branch::count())
                ->description(
                        'Current Branch: ' . (Branch::find($branchId)->name  ?? 'Not Selected')
                    )
                ->icon('heroicon-o-building-library')
                // ->color('success')
                ->url(BranchResource::getUrl('index'))
                ,

            Stat::make('Rooms', Room::where('branch_id', $branchId)->count())
                ->description('Total rooms')
                ->url(RoomResource::getUrl('index'))
                ->icon('heroicon-o-building-office'),

            Stat::make('Products', Product::where('branch_id', $branchId)->count())
                ->description('Total products')
                ->icon('heroicon-o-cube'),


            Stat::make('Therapists', Therapist::where('branch_id', $branchId)->count())
                ->description('Total therapists')
                ->icon('heroicon-o-user'),

            Stat::make('Customers', User::with('customerInfo')->whereHas('customerInfo', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })->count())
                ->description('Total Customers')
                ->icon('heroicon-o-users'),

            Stat::make('In Sessions',
                    DailyRoomRecord::where('record_date', now()->toDateString())
                        ->with('room', 'therapist', 'therapistType')
                        ->where('start_time' , '<=', now()->format('Y-m-d\TH:i'))
                        ->where('end_time', '>=', now()->format('Y-m-d\TH:i'))->get()->count()
                )
                // ->description('In-Session')
                ->description( 'Total Sessions for today: ' .
                    DailyRoomRecord::where('record_date', now()->toDateString())
                        ->with('room', 'therapist', 'therapistType')
                        // ->whereNull('invoice_id')
                        ->get()->count())
                ->icon('heroicon-o-calendar'),

            //  Stat::make('Branches', Branch::count())
            //     ->description('32k increase')
            //     ->descriptionIcon('heroicon-m-arrow-trending-up')
            //     ->chart([7, 2, 10, 3, 15, 4, -7])
            //     ->color('success'),
        ];
    }
}