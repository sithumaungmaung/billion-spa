<?php

namespace App\Filament\Pages;

use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Product;
use Filament\Pages\Page;
use App\Models\ProductSale;
use Ramsey\Uuid\Type\Integer;
use App\Models\DailyRoomRecord;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class CheckBill extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.check-bill';

    public ?string $bill_for = null;

    protected array $queryString = [
        'bill_for',
    ];


    //

    protected static ?string $navigationLabel = 'Rooms';

    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.pages.room-mangement') => 'Room Management',
            route('filament.admin.pages.check-bill') => 'Check Bill'
        ];
    }



    public array $roomIds = [];
    public $billItems, $billRooms = [];
    public $total;
    public $onePlusone = 1;


    public function mount(): void
    {

        $this->roomIds = array_map(
            'intval',
            explode(',', $this->bill_for)
        );
        // $this->getBill();
        $this->billItems = $this->getBillItems();
        $this->billRooms = $this->getBillRooms();
        $this->total = $this->totalBill();

    }

    public function getBillItems()
    {
        $billItems = ProductSale::whereIn('daily_room_record_id', $this->roomIds)->with('product')->get();
        return $billItems;
    }

    public function totalBill() {
        $total = 0;
        foreach ($this->billItems as $item) {
            $total += $item->quantity * $item->unit_price;
        }

        foreach ($this->billRooms as $billRoom) {
            $total += $billRoom['total_price'];
        }

        return $total;
    }

    public function getBillRooms()
    {
        $dailyRooms = DailyRoomRecord::whereIn('id', $this->roomIds)->with('room')->get();

        $grouped = $dailyRooms->groupBy(fn ($item) =>
            $item->room_id . '-' . $item->service_type
        );

        $items = [];

        foreach ($grouped as $group) {
            $first = $group->first();

            $totalSlots = $group->count();
            $paidSlots  = (int) ceil($totalSlots / $this->onePlusone);
            $freeSlots  = $totalSlots - $paidSlots;

            $price = $first->price;

            $serviceType = $first->service_type > 0 ? 'By Name' : 'Normal';

            $items[] = [
                'branch_id' => 1,
                'room_id'     => $first->room_id,
                'service_type' =>  $first->service_type,
                'room_name'   => "{$first->room->name} – {$serviceType}",
                'quantity'    => $paidSlots,
                'unit_price'  => $price,
                'total_price' => $paidSlots * $price,
            ];

            if($freeSlots > 0) {
                $items[] = [
                    'branch_id' => 1,
                    'room_id'     => $first->room_id,
                    'room_name'   => "{$first->room->name} – {$serviceType}",
                    'service_type' =>  $first->service_type,
                    'quantity'    => $freeSlots,
                    'unit_price'  => 0,
                    'total_price' => 0,
                ];
            }
        }

        return $items;
    }

    public function applyOnePlusOne() {
        $this->billRooms = $this->getBillRooms();
    }

    public function confirmBill() {

        $dailyRoomBillChecked = DailyRoomRecord::whereIn('id', $this->roomIds)->whereNotNull('invoice_id')->count();
        if($dailyRoomBillChecked > 0) {
            Notification::make()
            ->title('Billing Error')
            ->body('This room has already been billed.')
            ->danger()
            ->send();

            return;
        }

        $invoice = new Invoice();
        $invoice->branch_id = 1;
        $invoice->user_id = Auth::id();
        $invoice->invoice_no = $this->getInvoiceNo();
        $invoice->invoice_datetime = Carbon::now();
        $invoice->sub_total = $this->total;
        $invoice->discount = $this->total;
        $invoice->tax = $this->total;
        $invoice->grand_total = $this->total;
        $invoice->save();

        $invoice->invoiceRooms()->createMany($this->billRooms);
        $billItems = $this->getBillItemsForSave();

        if($billItems){
            $invoice->invoiceProducts()->createMany($billItems);
        }

        DailyRoomRecord::whereIn('id', $this->roomIds)->update(['invoice_id' => $invoice->id]);
        ProductSale::whereIn('daily_room_record_id', $this->roomIds)->update(['invoice_id' => $invoice->id]);
    }

    public function getInvoiceNo()
    {
        $today = date("mY");
        $month = date("m");
        $year = date("Y");
        $row = Invoice::whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->where('branch_id' , 1)
                ->count();
        $number = $row + 1;

        $invID = str_pad($number, 5, '0', STR_PAD_LEFT);
        return 'IN'. 'BSPA' . $today . $invID;
    }

    public function getBillItemsForSave()
    {
        $items = [];
        foreach ($this->billItems as $key => $billItems) {
            $items[] = [
                'branch_id' => 1,
                'quantity'    => $billItems->quantity,
                'product_id'  => $billItems->product_id,
                'unit_price'  => $billItems->unit_price,
                'total_price' => $billItems->total_price,
            ];
        }
        return $items;
    }
}
