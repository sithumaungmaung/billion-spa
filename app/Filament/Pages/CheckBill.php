<?php

namespace App\Filament\Pages;

use App\Models\Product;
use Filament\Pages\Page;
use App\Models\ProductSale;
use App\Models\DailyRoomRecord;
use Ramsey\Uuid\Type\Integer;

class CheckBill extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.check-bill';

    public ?string $bill_for = null;

    protected array $queryString = [
        'bill_for',
    ];

    public array $roomIds = [];
    public $billItems, $billRooms = [];
    public $total;

    public function mount(): void
    {   
        $this->roomIds = array_map(
            'intval',
            explode(',', $this->bill_for)
        );
        $this->billItems = $this->getBillItems();
        $this->billRooms = $this->getBillRooms();
        $this->total = $this->totalBill();
    }

    public function getBillItems()
    {
        $billItems = ProductSale::whereIn('daily_room_record_id', $this->roomIds)->get();
        return $billItems;
    }

    public function getBillRooms()
    {
        $billRooms = DailyRoomRecord::whereIn('id', $this->roomIds)->with('room')->get();
        return $billRooms;
    }

    public function totalBill() {
        $total = 0;
        foreach ($this->billItems as $item) {
            $total += $item->quantity * $item->unit_price;
        }

        foreach ($this->billRooms as $item) {
            $total += $item->price;
        }

        return $total;
    }
}
