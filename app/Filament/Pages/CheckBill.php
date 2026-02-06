<?php

namespace App\Filament\Pages;

use Carbon\Carbon;
use App\Models\Invoice;
use Filament\Pages\Page;
use App\Traits\BillTraits;
use App\Models\ProductSale;
use App\Models\DailyRoomRecord;
use App\Models\ExtraServiceSale;
use App\Models\InvoiceExtraService;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;


class CheckBill extends Page
{
    use BillTraits;

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
    public $billItems, $billExtraServices, $billRooms, $systemDailyRecords = [];
    public $total;
    public $buy = 1;
    public $free = 0;


    public function mount(): void
    {
        $this->roomIds = array_map(
            'intval',
            explode(',', $this->bill_for)
        );
        // $this->getBill();
        $this->billItems = $this->getBillItems($this->roomIds);
        $this->billExtraServices = $this->getBillExtraServices($this->roomIds);
        $this->billRooms = $this->getBillRooms($this->roomIds, $this->buy, $this->free);
        $this->total = $this->totalBill();
        $this->systemDailyRecords = $this->getSystemDailyRecords();
    }

    public function totalBill() {
        $total = 0;
        foreach ($this->billItems as $item) {
            $total += $item->quantity * $item->unit_price;
        }

        foreach ($this->billRooms as $billRoom) {
            $total += $billRoom['total_price'];
        }

        foreach ($this->billExtraServices as $extraService) {
            $total +=  $extraService->unit_price;
        }

        return $total;
    }

    public function getSystemDailyRecords() {
        $dailyRooms = DailyRoomRecord::whereIn('id', $this->roomIds)->with('room', 'therapist', 'therapistType')->get();
        return $dailyRooms;
    }

    public function applyPromotion() {
        $this->billRooms = $this->getBillRooms($this->roomIds, $this->buy, $this->free);
        $this->total = $this->totalBill();
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


        $billRooms = collect($this->billRooms)->map(function ($r) {
           $r['service_type'] = (int) data_get($r['service_type'], 'id', $r['service_type']);
            return $r;
        })->toArray();


        $invoice = new Invoice();
        $invoice->branch_id = 1;
        $invoice->user_id = Auth::id();
        $invoice->invoice_no = $this->getInvoiceNo();
        $invoice->invoice_datetime = Carbon::now();
        $invoice->buy = $this->buy;
        $invoice->free = $this->free;
        $invoice->sub_total = $this->total;
        $invoice->discount = 0;
        $invoice->tax = $this->total;
        $invoice->grand_total = $this->total;
        $invoice->save();

        $invoice->invoiceRooms()->createMany($billRooms);
        $billItems = $this->getBillItemsForSave();
        $billExtraServices = $this->getBillExtraServicesForSave();


        if($this->billExtraServices->count() > 0) {
          foreach($this->billExtraServices as $extraService) {
            $invoice->invoiceExtraServices()->create([
                'branch_id' => 1,
                'quantity'    => 1,
                'extra_service_id'  => $extraService->extra_service_id,
                'unit_price'  => $extraService->unit_price,
                'total_price' => $extraService->total_price,
            ]);
          }
        }

        if($billItems){
            $invoice->invoiceProducts()->createMany($this->billItems->toArray());
        }

        DailyRoomRecord::whereIn('id', $this->roomIds)->update(['invoice_id' => $invoice->id]);
        ProductSale::whereIn('daily_room_record_id', $this->roomIds)->update(['invoice_id' => $invoice->id]);
        ExtraServiceSale::whereIn('daily_room_record_id', $this->roomIds)->update(['invoice_id' => $invoice->id]);

        return redirect()->route('filament.admin.pages.invoice-detail', ['invoice_no' => $invoice->invoice_no]);
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

    public function getBillExtraServicesForSave()
    {
        $items = [];
        foreach ($this->billExtraServices as $key => $service) {
            $items[] = [
                'branch_id' => 1,
                'quantity'    => 1,
                'extra_service_id'  => $service->extra_service_id,
                'unit_price'  => $service->unit_price,
                'total_price' => $service->total_price,
            ];
        }
        return $items;
    }

    public function printPreview()
    {

        $url = route('invoice.preview', [
            'ids' => implode(',', $this->roomIds),
            'buy' => $this->buy,
            'free' => $this->free,
        ]);

        $this->dispatch('open-new-tab', url: $url);

        // return redirect()->away()->route('invoice.preview', ['ids' => implode(',', $this->roomIds), 'onePlusOne' => $this->onePlusone]);
    }

}
