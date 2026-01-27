<?php

namespace App\Filament\Pages;

use Carbon\Carbon;
use App\Models\Invoice;
use Filament\Pages\Page;
use App\Models\ProductSale;
use App\Models\DailyRoomRecord;
use App\Traits\BillTraits;
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
    public $billItems, $billRooms, $systemDailyRecords = [];
    public $total;
    public $onePlusone = 1;


    public function mount(): void
    {
        $this->roomIds = array_map(
            'intval',
            explode(',', $this->bill_for)
        );
        // $this->getBill();
        $this->billItems = $this->getBillItems($this->roomIds);
        $this->billRooms = $this->getBillRooms($this->roomIds, $this->onePlusone);
        $this->total = $this->totalBill();
        $this->systemDailyRecords = $this->getSystemDailyRecords();
    }

    // public function getBillItems()
    // {
    //     $billItems = ProductSale::whereIn('daily_room_record_id', $this->roomIds)->with('product')->get();
    //     return $billItems;
    // }

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

    public function getSystemDailyRecords() {
        $dailyRooms = DailyRoomRecord::whereIn('id', $this->roomIds)->with('room')->get();
        return $dailyRooms;
    }

    public function applyOnePlusOne() {
        $this->billRooms = $this->getBillRooms($this->roomIds, $this->onePlusone);
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

    // public function assign() {

    //     $this->billItems = $this->getBillItems();
    //     $this->billRooms = $this->getBillRooms();
    //     $this->total = $this->totalBill();

    //     $invoice = ['rooms' => $this->billRooms, 'items' => $this->billItems->toArray(), 'total' => $this->total];

    //     $pdf = Pdf::loadView('pdf.bill', ['invoice' => $invoice]);

    //     return $pdf->stream("invoice-{$invoice['rooms'][0]['room_id']}.pdf");
    // }

    public function printPreview()
    {

        $url = route('invoice.preview', [
            'ids' => implode(',', $this->roomIds),
            'onePlusOne' => $this->onePlusone
        ]);

        $this->dispatch('open-new-tab', url: $url);

        // return redirect()->away()->route('invoice.preview', ['ids' => implode(',', $this->roomIds), 'onePlusOne' => $this->onePlusone]);
    }

}
