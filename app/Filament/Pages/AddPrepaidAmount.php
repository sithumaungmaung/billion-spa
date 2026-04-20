<?php

namespace App\Filament\Pages;

use App\Models\CustomerPrepaidTransaction;
use App\Models\User;
use BackedEnum, UnitEnum;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;

class AddPrepaidAmount extends Page
{
    protected string $view = 'filament.pages.add-prepaid-amount';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $title = 'Add Prepaid Amount';

    protected static ?string $navigationLabel = 'Prepaid';

    protected static string | UnitEnum | null  $navigationGroup = 'Finance';

    public float $amount = 0;

    //  -----------------------------------

    public ?array $data = [];
    public $selectedCustomer = null;
    public $currentAmount = 0;
    public $prepaidAmount = 0;

    public function mount(): void
    {
         $this->customer_form->fill();
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
                        $this->currentAmount = $selectedCustomer->customerInfo?->current_amount ?? 0;
                    }else{
                        $this->selectedCustomer = null;
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

    public function AddPrepaidAmount()
    {
        $customerInfo = $this->selectedCustomer?->customerInfo;

        if (!$customerInfo ||!$this->selectedCustomer) return;

        if ($customerInfo->current_amount != 0)
        {
            $customerInfo->prepaid_amount = $customerInfo->prepaid_amount + $this->prepaidAmount;
            $customerInfo->current_amount = $customerInfo->current_amount + $this->prepaidAmount;

        }else
        {
            $customerInfo->current_amount = $this->prepaidAmount;
            $customerInfo->prepaid_amount = $this->prepaidAmount;
        }
            $customerInfo->save();
            $this->addPrepaidTransaction();

        $this->notifySuccess(
            'Success: Add Success',
            'Successfully Added Prepaid Amount.'
        );


    }


    private function addPrepaidTransaction()
    {

        $transaction = new CustomerPrepaidTransaction();
        $transaction->transaction_no = $this->getTransactionId();
        $transaction->customer_info_id = $this->selectedCustomer->customerInfo->id;
        $transaction->user_id = $this->selectedCustomer->id;
        $transaction->branch_id = $this->selectedCustomer->customerInfo->branch_id;
        $transaction->amount = $this->prepaidAmount;
        $transaction->transaction_type = 'credit';
        $transaction->reference_type = 'prepaid_topup';
        $transaction->reference_id = null;
        $transaction->balance = $this->currentAmount + $this->prepaidAmount;
        $transaction->transaction_date = now();
        $transaction->payment_method = 'Cash'; // Cash / KbZ,AYA pay / Bank Acc
        $transaction->note = "Add Prepaid Amount";
        // $transaction->status = $status;
        $transaction->save();

    }

    private function getTransactionId()
    {
        $today = date("mY");
        $month = date("m");
        $year  = date("Y");

        $customer = $this->selectedCustomer;
        $amount   = $this->prepaidAmount;
        // $branch   = $customer->customerInfo->branch_id;

        $transactionNo = sprintf(
            'TX-1-%s%s-%s-%s',
            // $branch,
            $year,
            $month,
            "Prepaid",
            rand(100, 999)
        );
        return $transactionNo;

    }

    public function notifyError(string $type, string $message): void
    {
        Notification::make()
                ->title($type)
                ->body($message)
                ->danger() // Makes the notification red
                ->persistent() // Stays on screen until they click it
                ->duration(1500)
                ->send();
    }
    public function notifySuccess(string $type, string $message): void
    {
        Notification::make()
                ->title($type)
                ->body($message)
                ->success() // Makes the notification red
                ->persistent() // Stays on screen until they click it
                ->duration(1500)
                ->send();
    }


}