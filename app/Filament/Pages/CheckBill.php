<?php

namespace App\Filament\Pages;

use App\Models\Product;
use App\Models\ProductSale;
use Filament\Pages\Page;

class CheckBill extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.check-bill';

    public ?string $bill_for = null;

    protected array $queryString = [
        'bill_for',
    ];

    public array $roomIds = [];
    public $billItems = [];

    public function mount(): void
    {
        if ($this->bill_for) {
            $this->roomIds = array_map(
                'intval',
                explode(',', $this->bill_for)
            );
            $this->billItems = $this->getBillItems();
        }
    }

    public function getBillItems()
    {
        $billItems = ProductSale::whereIn('daily_room_record_id', $this->roomIds)->get();
        return $billItems;
    }
}
