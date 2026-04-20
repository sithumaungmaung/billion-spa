<?php

namespace App\Filament\Pages;

use App\Models\CustomerInfo;
use App\Models\CustomerPrepaidTransaction;
use App\Models\DailyRoomRecord;
use App\Models\ExtraServiceSale;
use App\Models\Invoice;
use App\Models\ProductSale;
use App\Models\User;
use App\Traits\BillTraits;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;



class CheckBill extends Page
{
    use BillTraits, InteractsWithForms;


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
            route('filament.admin.pages.room-management') => 'Room Management',
            route('filament.admin.pages.check-bill') => 'Check Bill'
        ];
    }

    public array $roomIds = [];
    public $billItems, $billExtraServices, $billRooms, $systemDailyRecords = [];
    public $total;
    public $grandTotal = 0;
    public $buy = 1;
    public $free = 0;
    public $discountPercentage = 0;
    public $discountAmountByPercentage = 0;
    public $serviceChargePercentage = 0;
    public $serviceChargeAmount = 0;

    public $subTotal = 0;
    public $totalDiscountAmount = 0;

    public $applyProductDiscount = [];
    public $totalProductDiscount = 0;

    public $user = null;

    //  Prepaid
    public $invoiceId = null;
    public $selectedCustomer = null;
    public $availableAmount = 0;
    public $deductionAmount = null;
    public $outstandingAmount = null;
    public $note = '';

    public ?array $data = [];

    public function mount(): void
    {
        $this->roomIds = array_map(
            'intval',
            explode(',', $this->bill_for)
        );

        $this->billItems = $this->getBillItems($this->roomIds);
        $this->billExtraServices = $this->getBillExtraServices($this->roomIds);
        $this->billRooms = $this->getBillRooms($this->roomIds, $this->buy, $this->free);
        // $this->total = $this->totalBill();
        $this->total = $this->getSubTotal();

        $this->user = Auth::user();

        $this->subTotal = $this->getSubTotal();
        $this->systemDailyRecords = $this->getSystemDailyRecords();

        // prepaid
         $this->customer_form->fill();
    }

    public function totalBill()
    {
        $total = 0;
        foreach ($this->billItems as $item) {
            $total += $item->quantity * $item->unit_price;
        }

        foreach ($this->billRooms as $billRoom) {
            $total += $billRoom['total_price'];
        }

        foreach ($this->billExtraServices as $extraService) {
            $total +=  $extraService->total_price;
        }

        return $total;
    }


    public function getSystemDailyRecords()
    {
        $dailyRooms = DailyRoomRecord::whereIn('id', $this->roomIds)->with('room', 'therapist', 'therapistType')->get();
        return $dailyRooms;
    }

    public function applyPromotion()
    {
        $this->billRooms = $this->getBillRooms($this->roomIds, $this->buy, $this->free);
        $this->subTotal = $this->getSubTotal();
        $this->total = $this->getGrandTotal();
        $this->updatedTotal();

    }

    public function confirmBill()
    {

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
        $invoice->sub_total = $this->getSubTotal();
        $invoice->tax = 0 ;
        $invoice->grand_total = $this->getGrandTotal();
        $invoice->discount_percent = $this->discountPercentage;
        $invoice->discount_amount = $this->discountAmountByPercentage;
        $invoice->service_charge_percent = $this->serviceChargePercentage;
        $invoice->service_charge = $this->serviceChargeAmount;
        $invoice->save();

        $invoice->invoiceRooms()->createMany($billRooms);
        $billItems = $this->getBillItemsForSave();
        $billExtraServices = $this->getBillExtraServicesForSave();


        if($this->billExtraServices->count() > 0) {
          foreach($this->billExtraServices as $extraService) {

            $invoice->invoiceExtraServices()->create([
                'branch_id' => 1,
                'extra_service_title' => $extraService->extraService->title,
                'quantity'    => $extraService->quantity,
                'extra_service_id'  => $extraService->extra_service_id,
                'unit_price'  => $extraService->unit_price,
                'total_price' => $extraService->total_price,
            ]);
          }
        }

        if($billItems){
            $invoice->invoiceProducts()->createMany($billItems);
        }

        DailyRoomRecord::whereIn('id', $this->roomIds)->update(['invoice_id' => $invoice->id]);
        ProductSale::whereIn('daily_room_record_id', $this->roomIds)->update(['invoice_id' => $invoice->id]);
        ExtraServiceSale::whereIn('daily_room_record_id', $this->roomIds)->update(['invoice_id' => $invoice->id]);

        $this->invoiceId = $invoice->id;

        if($this->selectedCustomer)
        {
           $this->payWithPrepaid();
        }

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
            $unit_discount = in_array($billItems->id, collect($this->applyProductDiscount)->keys()->toArray()) ? $this->applyProductDiscount[$billItems->id] : 0;
            $items[] = [
                'branch_id' => 1,
                'quantity'    => $billItems->quantity,
                'product_id'  => $billItems->product_id,
                'product_name' => $billItems->product->name,
                'product_code' => $billItems->product->product_code,
                'unit_price'  => $billItems->unit_price,
                'unit_discount' => $unit_discount,
                'total_price' => $billItems->total_price - ($unit_discount * $billItems->quantity),
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
            'discountPercentage' => $this->discountPercentage,
            'discountAmountByPercentage' => $this->discountAmountByPercentage,
            'serviceChargePercentage' => $this->serviceChargePercentage,
            'serviceChargeAmount' => $this->serviceChargeAmount,
            'productDiscount' => json_encode($this->applyProductDiscount),
            // 'totalDiscountAmount' => $this->totalDiscountAmount,
            'totalProductDiscount' => $this->totalProductDiscount,
            'subTotal' => $this->getSubTotal(),
            'grandTotal' => $this->getGrandTotal(),
        ]);

        $this->dispatch('invoice.preview', url: $url);
    }


    // main Dis By %
    public function updatedDiscountPercentage()
    {
        $this->discountAmountByPercentage = $this->discountPercentage * $this->getSubTotal() / 100;
        $this->totalDiscountAmount = $this->discountAmountByPercentage + $this->totalProductDiscount;

        $this->subTotal = $this->getSubTotal();
        $this->total = $this->getGrandTotal();
        $this->updatedTotal();

    }

    // Service %
    public function updatedServiceChargePercentage()
    {
        $this->serviceChargeAmount = $this->serviceChargePercentage * $this->getSubTotal() / 100;
        $this->subTotal = $this->getSubTotal();
        $this->total = $this->getGrandTotal() ;
        $this->updatedTotal();
    }

    // Product %
    public function updatedApplyProductDiscount($disPrice, $sale_id)
    {

        $this->totalProductDiscount = 0;
        foreach ($this->applyProductDiscount as $saleId => $price) {

            if($price === null || $price === '' || $price == 0){
                unset($this->applyProductDiscount[$saleId]);
            }

            $item = $this->billItems->where('id', $saleId)->first();
            $quantity = $this->billItems->where('id', $saleId)->first()->quantity;
            $this->totalProductDiscount += (int) $price * (int) $quantity ?? 0;

            if($price > $item->unit_price){
                $this->applyProductDiscount[$saleId] = $item->unit_price;
            }
        }

        $this->subTotal = $this->getSubTotal();
        $this->total = $this->getGrandTotal();
        $this->updatedTotal();


        $this->updatedDiscountPercentage();
        $this->updatedServiceChargePercentage();


    }

    public function getSubTotal()
    {
        return $this->totalBill() - $this->totalProductDiscount;
    }

    public function updatedTotal()
    {
        $this->deductionWithPrepaidForTotal();
    }

    public function getGrandTotal()
    {
        return $this->subTotal + $this->serviceChargeAmount - $this->discountAmountByPercentage;
    }



    public function updatedBuy()
    {
        $this->applyPromotion();
    }


    protected function customer_form(Schema $schema): Schema
    {
        return $schema
            ->components([
             Select::make('user_id')
                ->hiddenLabel()
                // ->label('Customer')
                ->placeholder('Select Customer')
                ->options( $this->getCustomer())
                ->searchable()
                ->preload()
                ->live()

               ->afterStateUpdated(function ($state) {
                    if($state){

                        $selectedCustomer = User::where('id', $state)->with('customerInfo')->first();
                        $this->selectedCustomer = $selectedCustomer;
                        $this->availableAmount = $selectedCustomer->customerInfo?->current_amount ?? 0;
                        $this->outstandingAmount = null;
                        $this->deductionWithPrepaidForTotal();
                    }else{
                        $this->selectedCustomer = null;
                        $this->availableAmount = 0;
                        $this->outstandingAmount = null;
                          $this->deductionWithPrepaidForTotal();
                    }
                })
                ->native(true), // Forces the nice UI even on mobile
            ])
            ->statePath('data');
    }

    public function getCustomer(): array
    {
        return User::role('customer')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function deductionWithPrepaidForTotal()
    {
        if($this->availableAmount < $this->total){
            $this->deductionAmount = $this->availableAmount;
        }else{
            $this->deductionAmount = $this->total;
        }
    }

    public function payWithPrepaid()
    {
        $customer = CustomerInfo::where('user_id',$this->selectedCustomer->id)->first();

        if($customer->current_amount < $this->total)
        {
            $this->outstandingAmount =  $this->total - $customer->current_amount;
            $this->availableAmount = 0;
            $this->note = "split payment";
            $customer->current_amount = 0;
        }
        else
        {
            $customer->current_amount = $customer->current_amount - $this->deductionAmount;
            $this->availableAmount = $this->availableAmount - $this->deductionAmount;
            $this->note = "fully prepaid payment";
        }
        $status = 1;
        $customer->save();
        $this->createPrepaidTransaction($status);

    }

    private function createPrepaidTransaction($status)
    {

        $transaction = new CustomerPrepaidTransaction();
        $transaction->transaction_no = $this->getTransactionId();
        $transaction->customer_info_id = $this->selectedCustomer->customerInfo->id;
        $transaction->user_id = $this->selectedCustomer->id;
        $transaction->branch_id = $this->selectedCustomer->customerInfo->branch_id;
        $transaction->amount = $this->deductionAmount;
        $transaction->transaction_type = 'use';
        $transaction->reference_type = 'invoice';
        $transaction->reference_id = $this->invoiceId;
        $transaction->balance = $this->availableAmount;
        $transaction->transaction_date = now();
        $transaction->payment_method = 'prepaid';
        $transaction->note = $this->note;
        $transaction->status = $status;
        $transaction->save();

    }


    private function getTransactionId()
    {
        $today = date("mY");
        $month = date("m");
        $year  = date("Y");

        $customer = $this->selectedCustomer;
        $amount   = $this->deductionAmount;
        $invoice  = $this->invoiceId;
        // $branch   = $customer->customerInfo->branch_id;

        $transactionNo = sprintf(
            'TX-1-%s%s-%s-%s',
            // $branch,
            $year,
            $month,
            $invoice,
            rand(100, 999)
        );
        return $transactionNo;

    }



}