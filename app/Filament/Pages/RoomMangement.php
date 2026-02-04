<?php

namespace App\Filament\Pages;

use BackedEnum;
use Carbon\Carbon;
use App\Models\Room;
use App\Models\Product;
use App\Models\TimeSlot;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\Therapist;
use App\Models\ProductSale;
use App\Models\ExtraService;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use App\Models\TherapistType;
use App\Models\DailyRoomRecord;
use App\Models\ExtraServiceSale;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;

class RoomMangement extends Page implements HasForms
{

    use InteractsWithForms;

    protected string $view = 'filament.pages.room-mangement';
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-text'; // Replace 'heroicon-o-document-text' with your desired icon

    public string $date;
    public $rooms;
    public $timeSlots;
    public $therapists;
    public $products;

    public ?int $selectedRoomId = null;
    public ?array $selectedRoomIdsForBill = [];
    public ?int $selectedSlotId = null;
    public ?int $selectedTherapistId = null;
    public ?int $selectedProductId = null;
    public ?int $searchRoomId = 0;
    public ?int $selectedType = null;
    public ?int $selectedProductQty = 1;
    public $therapistTypes;
    public $extraServices; // for dropdown (mount action)
    public ?int $selectedExtraServiceId = null; // for dropdown

    public $isExistingRecord = null;

    public $sameTimeSlotForTherapist = [];

    // for detail section view (Mini Info)
    public string $selectedRoomName = '';
    public string $selectedTherapistName = '';
    public string $selectedTherapistType = '';
    public string $selectedTimeSection = '';
    public string $selectedStartTime = '';
    public string $selectedEndTime = '';
    public ?array $selectedExtraServicesList = [];

    public ?string $selectedProductSaleId = null;

    public ?array $data = [];

    public function mount(): void
    {
        $this->date = now()->toDateString();
        $this->rooms = Room::get();
        $this->timeSlots = TimeSlot::get();
        $this->therapists = $this->getFreeTherapists();
        $this->therapistTypes = $this->getTherapistTypes();
        $this->selectedTherapistType = $this->therapistTypes->first()->id;

        $this->therapist_form->fill();
        $this->room_form->fill();
        $this->product_form->fill();


        $this->products = Product::get();
        $this->extraServices = ExtraService::get();
    }


    public function selectCell(int $roomId, int $slotId): void
    {

        $this->selectedRoomId = $roomId;
        $this->selectedSlotId = $slotId;

        $this->selectedRoomName = Room::find($roomId)->name;
        $this->selectedTherapistName = $this->getThapistName($roomId, $slotId);
        $this->selectedTherapistType = $this->getTherapistType($roomId, $slotId);
        $this->selectedExtraServicesList = $this->getRoomExtraServices($roomId, $slotId);

        $this->selectedStartTime = TimeSlot::find($slotId)->start_time;
        $this->selectedEndTime =  TimeSlot::find($slotId)->end_time;
        $this->selectedTimeSection = $this->selectedStartTime . ' - ' . $this->selectedEndTime;

        $this->isExistingRecord = DailyRoomRecord::where('record_date', $this->date)
        ->where('room_id', $this->selectedRoomId)
        ->where('time_slot_id', $this->selectedSlotId)
        ->first();

        $this->sameTimeSlotForTherapist = $this->getSameTimeSlotTherapist($roomId, $slotId);

        // $this->mountAction('cellModal'); {{ to show up the modal box }}
        //  $this->reset(['selectedRoomId', 'selectedSlotId', 'selectedTherapistId']);
    }

    public function assign(): void
    {
        $dailyRoomRecord = DailyRoomRecord::where('record_date', $this->date)
                                            ->where('room_id', $this->selectedRoomId)
                                            ->where('time_slot_id', $this->selectedSlotId)
                                            ->whereNotNull('invoice_id')
                                            ->first();

        if ($dailyRoomRecord) {
            Notification::make()
            ->title('Billing Error')
            ->body('This room has already been billed.')
            ->danger()
            ->send();

            return;
        }

        $this->selectedTherapistId = $this->data['therapist_id'] ?? null;
        $room = Room::find($this->selectedRoomId);

        $therapistType = TherapistType::find($this->selectedType);

        $alreadyAssigned = DailyRoomRecord::where([
            'record_date' => $this->date,
            'time_slot_id' => $this->selectedSlotId,
            'therapist_id' => $this->selectedTherapistId,
             'service_type' => $this->selectedType,
             'service_type_price' => $therapistType ? $therapistType->price : 0,
        ])->first();

        if($alreadyAssigned) {
            $this->notifyError(
                    'Error: Assignment Error',
                    'You have already selected a therapist for this time slot. Please select a different therapist.'
                );
                return;

        }

        DailyRoomRecord::updateOrCreate(
            [
                'record_date' => $this->date,
                'room_id' => $this->selectedRoomId,
                'time_slot_id' => $this->selectedSlotId,
            ],
            [
                'therapist_id' => $this->selectedTherapistId,
                'service_type' => $this->selectedType,
                'price' => $room ? $room->price : 0,
                'service_type_price' => $therapistType ? $therapistType->price : 0,
            ]
        );

        $this->reset(['selectedRoomId', 'selectedSlotId']);

        $this->data['therapist_id'] = null;
        $this->therapist_form->fill();

    }

    public function removeTherapist(): void
    {
        $therapist = DailyRoomRecord::where([
            'record_date' => $this->date,
            'room_id' => $this->selectedRoomId,
            'time_slot_id' => $this->selectedSlotId
        ])->first();

        if($therapist->saleProducts->count() > 0) {
            $this->notifyError(
                'Error: Remove Error',
                'You cannot unassign a therapist with products. Please remove the products first.'
            );return;
        }else{
            $therapist->delete();
            $this->notifySuccess(
                'Success: Remove Success',
                'Unassigned successfully.'
            );return;
        }
    }

    public function addProduct(): void
    {
        $dailyRoomRecord = DailyRoomRecord::where('record_date', $this->date)->where('room_id', $this->selectedRoomId)
                                            ->where('time_slot_id', $this->selectedSlotId)
                                            ->whereNull('invoice_id')
                                            ->first();

        $this->selectedProductId = $this->data['product_id'] ?? null;

        if (!$dailyRoomRecord) {
            Notification::make()
            ->title('Billing Error')
            ->body('This room has already been billed.')
            ->danger()
            ->send();

            return;
        }

        $productSale = ProductSale::where('daily_room_record_id', $dailyRoomRecord->id)
                                ->where('product_id', $this->selectedProductId)->first();

        $product = Product::where('id', $this->selectedProductId)->first();

        // dump($productSale ?? $productSale->toArray());

        if(!$product) {
            return;
        }

        if ($productSale) {
            $productSale->update([
                'quantity' => $productSale->quantity + $this->selectedProductQty,
                'unit_price' => $product->price,
                'branch_id' => 1,
                'total_price' => ($productSale->quantity + $this->selectedProductQty ) * $product->price
            ]);
        } else {

            ProductSale::create([
                'daily_room_record_id' => $dailyRoomRecord->id,
                'product_id' => $this->selectedProductId,
                'unit_price' => $product->price,
                'quantity' => $this->selectedProductQty ?? 1,
                'branch_id' => 1,
                'total_price' => $this->selectedProductQty  * $product->price
            ]);
        }

        $this->notifySuccess(
            'Success: Add Success',
            'Product added successfully.'
        );

        $this->reset(['selectedProductId']);
    }

    public function removeProduct($saleproduct): void
    {
        $productSale = ProductSale::where('id', $saleproduct)->delete();

        $this->notifySuccess(
            'Success: Remove Success',
            'Product removed successfully.'
        );

    }



    public function reduceProduct($saleProduct)
    {
        $productSale = ProductSale::where('id', $saleProduct)->first();
        $totalQty = $productSale->quantity - 1;
        $productSale->update([
            'quantity' => $totalQty,
            'total_price' => $totalQty * $productSale->unit_price
        ]);

        $this->notifySuccess(
            'Success: Reduce Success',
            'Product quantity reduced successfully.'
        );
        return;
    }

    public function addMoreProduct($saleProduct)
    {
        $productSale = ProductSale::where('id', $saleProduct)->first();
        $toalQty = $productSale->quantity + 1;
        $productSale->update([
            'quantity' => $toalQty,
            'total_price' => $toalQty * $productSale->unit_price
        ]);

        $this->notifySuccess(
            'Success: Add Success',
            'Product quantity added successfully.'
        );
        return;
    }


    public function addExtraService()
    {
        $alreadyExists = ExtraServiceSale::where('extra_service_id', $this->selectedExtraServiceId)->where('daily_room_record_id', $this->isExistingRecord->id)->first();

        if($this->isExistingRecord && !$alreadyExists){
            $price = ExtraService::where('id', $this->selectedExtraServiceId)->first()->price;
            ExtraServiceSale::create([
                'extra_service_id' => $this->selectedExtraServiceId,
                'daily_room_record_id' => $this->isExistingRecord->id,
                'branch_id' => 1,
                'unit_price' => $price,
                'total_price' => $price
            ]);
            $this->selectedExtraServicesList = $this->getRoomExtraServices($this->selectedRoomId, $this->selectedSlotId);

            $this->notifySuccess(
                'Success: Add Success',
                'Extra service added successfully.'
            );
        }else{
            $this->notifyError(
                'Error: Add Error',
                'You cannot add the same extra service more than once.'
            );
            return;
        }


    }


    public function removeExtraService($saleproduct): void
    {
        $productSale = ExtraServiceSale::where('id', $saleproduct)->delete();
        $this->selectedExtraServicesList = $this->getRoomExtraServices($this->selectedRoomId, $this->selectedSlotId);
        $this->notifySuccess(
            'Success: Remove Success',
            'Extra service removed successfully.'
        );
    }

    // public function updatedSelectedType(): void {
    //     $room = DailyRoomRecord::where('room_id', $this->selectedRoomId)->first();
    //     $room->service_type = $this->selectedType;
    //     $room->save();
    // }

    public function getCanAddProductProperty() : bool {
        $dailyRoomRecord = DailyRoomRecord::where('record_date', $this->date)
                            ->where('room_id', $this->selectedRoomId)
                            ->where('time_slot_id', $this->selectedSlotId)
                            ->first();

        $result = $dailyRoomRecord ? 0 : 1;
        return $result;
    }

    public function getThapistName($roomId, $slotId): string
    {
        $schedule = DailyRoomRecord::where([
            'record_date' => $this->date,
            'room_id' => $roomId,
            'time_slot_id' => $slotId,
        ])->with('therapist')->first();

        return $schedule?->therapist?->name ?? "";
    }

    public function getTherapistType($roomId, $slotId): string
    {
        $schedule = DailyRoomRecord::where([
            'record_date' => $this->date,
            'room_id' => $roomId,
            'time_slot_id' => $slotId,
        ])->with('therapistType')->first();

        return $schedule?->therapistType?->title ?? "";
    }

    public function getRoomExtraServices($roomId, $slotId): array
    {
        $schedule = DailyRoomRecord::where([
            'record_date' => $this->date,
            'room_id' => $roomId,
            'time_slot_id' => $slotId,
        ])->with('extraServices')->first();

            if (!$schedule) {
                return [];
            }

            $extraServiceAndSales = ExtraServiceSale::where('daily_room_record_id', $schedule->id)->with('extraService')->get();

            $extraServiceAndSales = [
                'extraServices' => $extraServiceAndSales->toArray(),
                'total' => $extraServiceAndSales->sum('unit_price')
            ];

            return $extraServiceAndSales ?? [];



    }

    public function checkBill($roomId, $slotId): bool
    {
        $billCheck = DailyRoomRecord::where([
            'record_date' => $this->date,
            'room_id' => $roomId,
            'time_slot_id' => $slotId,
        ])->whereNotNull('invoice_id')->count();

        return $billCheck ? true : false;
    }

    public function getDailyRoomRecordId($roomId, $slotId): string
    {
        $schedule = DailyRoomRecord::where([
            'record_date' => $this->date,
            'room_id' => $roomId,
            'time_slot_id' => $slotId,
        ])->select('id')->first();

        return $schedule?->id ?? "";
    }

    public function getSelectedRoomRecordProductsProperty(): ? Array
    {
        if ($this->selectedRoomId && $this->selectedSlotId) {

            $dailyRoomRecord = DailyRoomRecord::where([
                'record_date' => $this->date,
                'room_id' => $this->selectedRoomId,
                'time_slot_id' => $this->selectedSlotId,
            ])->first();

            if (!$dailyRoomRecord) {
                return [];
            }

            $productSale = ProductSale::with('product')->where('daily_room_record_id', $dailyRoomRecord->id)->get();

            return [
                'dailyRoomRecord' => $dailyRoomRecord,
                'productSales' => $productSale,
                'productSaleTotal' => $productSale->sum('total_price'),
            ];
        }

        return [];
    }

    public function goToCheckBill()
    {
        // if (! $this->selectedRoomId || ! $this->selectedSlotId) {
        //     return;
        // }
        return redirect()->route('filament.admin.pages.check-bill', [
            'bill_for' => implode(',', $this->selectedRoomIdsForBill)
        ]);
    }

    public function getFreeTherapists()
    {
        $now = Carbon::now()->format('H:i:s');

        $allStaffs = Therapist::all();
        $busyStaffIds = DailyRoomRecord::whereHas('timeSlot', function ($q) use ($now) {
                $q->where('start_time', '<=', $now)
                ->where('end_time', '>=', $now);
            })
            // ->where(['time_slot_id' => $this->selectedSlotId])
            // ->where('record_date', $this->date)
            ->with('therapist')
            ->pluck('therapist_id')
            ->unique()
            ->toArray();

            return $allStaffs->whereNotIn('id', collect($busyStaffIds)->merge($this->sameTimeSlotForTherapist)->unique())->values();
    }

    public function getTherapistTypes()
    {
        return TherapistType::all();
    }


    public function therapist_form(Schema $schema): Schema
    {

        return $schema
            ->components([ // In v4, we use ->components([]) instead of ->schema([])
                Select::make('therapist_id')
                    ->hiddenLabel()
                    ->placeholder('Select Therapist')
                    // Using a query makes it more efficient

                    ->options(fn () => $this->getFreeTherapists()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->live()
                    ->native(false), // Forces the nice UI even on mobile
            ])
            ->statePath('data');
    }

    public function room_form(Schema $schema): Schema
    {
        return $schema
            ->components([ // In v4, we use ->components([]) instead of ->schema([])
                Select::make('room_id')
                    ->hiddenLabel()
                    ->placeholder('Select Room')
                    // Using a query makes it more efficient
                    ->options(Room::all()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->live()
                    ->native(false), // Forces the nice UI even on mobile
            ])
            ->statePath('data');
    }

    public function pickRoom()
    {
        $this->searchRoomId = $this->data['room_id'] ?? null;
        // dump($this->selectedRoomId);
        // return;
    }


    public function product_form(Schema $schema): Schema
    {
        return $schema
            ->components([ // In v4, we use ->components([]) instead of ->schema([])
                Select::make('product_id')
                    ->hiddenLabel()
                    ->placeholder('Select Product')
                    // Using a query makes it more efficient
                    ->options(Product::all()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->live()
                    ->native(false), // Forces the nice UI even on mobile
            ])
            ->statePath('data');
    }


    public function getSameTimeSlotTherapist($roomId, $slotId)
    {
        return DailyRoomRecord::where(['time_slot_id' => $this->selectedSlotId])
        ->where('record_date', $this->date)->with('therapist')->pluck('therapist_id');
    }


    public function notifyError(string $type, string $message): void
    {
        Notification::make()
                ->title($type)
                ->body($message)
                ->danger() // Makes the notification red
                ->persistent() // Stays on screen until they click it
                ->send();
    }
    public function notifySuccess(string $type, string $message): void
    {
        Notification::make()
                ->title($type)
                ->body($message)
                ->success() // Makes the notification red
                ->persistent() // Stays on screen until they click it
                ->send();
    }








    // protected function getActions(): array
    // {
    //     return [
    //         Action::make('cellModal')
    //             ->modalHeading('Details')
    //             ->modalSubmitActionLabel('Save')
    //             // ->form([
    //             //      TextColumn::make('name')
    //             //     ->searchable(),
    //             // ])
    //             ->modalContent(fn () => view(
    //                 'filament.modals.section_detail',
    //                 [
    //                     'room' => Room::find($this->selectedRoomId),
    //                     'slot' => TimeSlot::find($this->selectedSlotId),
    //                     'therapist_name' => $this->getThapistName($this->selectedRoomId, $this->selectedSlotId),
    //                 ]
    //             ))
    //             ->action(function (array $data) {
    //                 Booking::create([
    //                     'room_id'      => $this->selectedRoomId,
    //                     'slot_id'      => $this->selectedSlotId,
    //                     'therapist_id' => $data['therapist_id'],
    //                 ]);
    //             }),
    //     ];
    // }


}
