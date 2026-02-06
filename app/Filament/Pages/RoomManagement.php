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

class RoomManagement extends Page implements HasForms
{

    use InteractsWithForms;

    protected string $view = 'filament.pages.room-management';
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-text'; // Replace 'heroicon-o-document-text' with your desired icon


    // ----------------

    public string $date;
    public $rooms;
    public ?int $selectedRoomId = 0;
    public  $selectedRoom = null;
    public ?array $checkRoomIds = [];

    //  Select for switch therapist
    public  $selectedTherapistTypeId = null;
    public ?int $selectedTherapistId = null;


    // Select for product add
    public ?int $selectedProductId = null;
    public ?int $selectedProductQty = 1;

    // Select for extra service
     public ?int $selectedExtraServiceId = null; // for dropdown
     public ?array $selectedExtraServicesList = [];


    // Default
    public $activeRooms;
    public $avaliableRooms;
    public $therapistTypes;
    public $extraServices;

    //  Detail

    public $extraServiceAndProductSales = [];

    // public ?array $selectedRoomIdsForBill = [];
    // public ?int $selectedSlotId = null;
    // public ?int $selectedTherapistId = null;

    // public ?int $selectedType = null;
    // public $therapistTypes;
    // public $extraServices; // for dropdown (mount action)

    // public $isExistingRecord = null;

    // public $sameTimeSlotForTherapist = [];

    // // for detail section view (Mini Info)
    // public string $selectedRoomName = '';
    // public string $selectedTherapistName = '';
    // public string $selectedTherapistType = '';
    // public string $selectedTimeSection = '';
    // public string $selectedStartTime = '';
    // public string $selectedEndTime = '';


    // public ?string $selectedProductSaleId = null;

    public ?array $data = [];

    public function mount(): void
    {
        $this->date = now()->toDateString();
        $this->avaliableRooms = $this->avaliavleRooms();
        $this->activeRooms = $this->activeRooms();
        $this->rooms =  $this->activeRooms;

        // dump($this->activeRooms->toArray());
        // $this->rooms =  $this->avaliableRooms;
        // dump($this->avaliableRooms->toArray());
        // dump($this->rooms->toArray());
        // $this->rooms = Room::get();
        // $this->timeSlots = TimeSlot::get();
        // $this->therapists = $this->getFreeTherapists();
        $this->therapistTypes = TherapistType::get();
        $this->extraServices = ExtraService::get();
        // $this->selectedTherapistType = $this->therapistTypes->first()->id;
        // dump($this->activeRooms->pluck('room_id')->toArray());
        $this->therapist_form->fill();
        $this->room_form->fill();
        $this->product_form->fill();




        // $this->products = Product::get();
        // $this->extraServices = ExtraService::get();
    }



    public function selectRoom(int $roomId)
    {
        $selectedRoom = DailyRoomRecord::with('room', 'therapist', 'therapistType', 'saleProducts')->where('id', $roomId)->first();
        $this->selectedRoom = $selectedRoom;
        $this->selectedRoomId = $selectedRoom->id;
        // $this->rooms = $this->selectedRoom ? [$selectedRoom] : $this->activeRooms;
        $this->selectedRoom = DailyRoomRecord::where('id', $selectedRoom->id)->first();
        $this->rooms =  $this->activeRooms();


        $this->data['therapist_id'] = $selectedRoom->therapist->id;

        $this->getSalesForDailyRoomRecord();

    }

    public function checkRoom(int $roomId)
    {
        if (in_array($roomId, $this->checkRoomIds)) {
            $key = array_search($roomId, $this->checkRoomIds);
            unset($this->checkRoomIds[$key]);
        }else{
            $this->checkRoomIds[] = $roomId;
            }

    }

    public function avaliavleRooms ()
    {
        $activeRooms = DailyRoomRecord::where('record_date', $this->date)
                ->with('room', 'therapist', 'therapistType')

                ->where('start_time' , '<=', now()->format('H:i:s'))
                ->where('end_time', '>=', now()->format('H:i:s'))->get();

        $originalRooms = Room::whereNotIn('id', $activeRooms->pluck('room_id'))->get();




    }

    public function activeRooms ()
    {
        $roomRecords = DailyRoomRecord::where('record_date', $this->date)
                        ->with('room', 'therapist', 'therapistType')
                        ->whereNull('invoice_id')
                        ->where('start_time' , '<=', now()->format('H:i:s'))
                        ->where('end_time', '>=', now()->format('H:i:s'))->get();


        return $roomRecords;
    }

    //  FORMS =============
    public function therapist_form(Schema $schema): Schema
    {

        return $schema
            ->components([ // In v4, we use ->components([]) instead of ->schema([])
                Select::make('therapist_id')
                    ->hiddenLabel()
                    ->placeholder('Select Therapist')
                    // Using a query makes it more efficient
                    ->options(fn () => $this->getTherapistOptions())
                    ->searchable()
                    ->preload()
                    ->live()
                    ->native(false) // Forces the nice UI even on mobile,
            ])
            ->statePath('data');
    }

    /// Helper
    public function getFreeTherapists()
    {
        $roomRecords = DailyRoomRecord::where('record_date', $this->date)
                        ->where('start_time' , '<=', now()->format('H:i:s'))
                        ->where('end_time', '>=', now()->format('H:i:s'))->get();
        return Therapist::whereNotIn('id', $roomRecords->pluck('therapist_id'))->get();
    }

    protected function getTherapistOptions(): array
    {

        $therapists = $this->getFreeTherapists();

        if ($this->selectedRoom?->therapist) {
            $therapists = $therapists->concat(collect([$this->selectedRoom->therapist]));
        }
        return $therapists->pluck('name', 'id')->toArray();
    }

    public function room_form(Schema $schema): Schema
    {
        return $schema
            ->components([ // In v4, we use ->components([]) instead of ->schema([])
                Select::make('room_id')
                    ->hiddenLabel()
                    ->placeholder('Select Room')
                    // Using a query makes it more efficient
                    ->options(Room::whereIn('id', $this->activeRooms()->pluck('room_id')->toArray())->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state) {
                        // $state === selected room_id


                        if($state){
                            $this->selectedRoomId = DailyRoomRecord::where('room_id', $state)->first()->id;
                            $this->getSalesForDailyRoomRecord();
                            $this->selectedRoom = DailyRoomRecord::where('room_id', $state)->first();
                            // dump(DailyRoomRecord::where('id', $this->selectedRoomId)->get());
                            $this->rooms =  DailyRoomRecord::where('id', $this->selectedRoomId)->get();
                            $this->data['therapist_id'] = $this->selectedRoom->therapist->id;
                            // dump($this->data['therapist_id']);

                        }else{
                            $this->rooms =  $this->activeRooms();
                            $this->selectedRoom = null;
                            $this->selectedRoomId = 0;
                            $this->extraServiceAndProductSales = [];
                             $this->data['therapist_id'] = null;
                        }
                    })
                    ->native(true), // Forces the nice UI even on mobile
            ])
            ->statePath('data');
    }

    public function product_form(Schema $schema): Schema
    {
        return $schema
            ->components([ // In v4, we use ->components([]) instead of ->schema([])
                Select::make('product_id')
                    ->hiddenLabel()
                    ->placeholder('Select Product')
                    // ->options(Product::all()->pluck('name', 'id'))
                    ->options(
                            Product::all()->mapWithKeys(function ($product) {
                                return [$product->id => $product->name . ' - (' . number_format($product->price) . ')'];
                            })->toArray()
                        )
                    ->searchable()
                    ->preload()
                    ->live()
                    ->native(false), // Forces the nice UI even on mobile


            ])
            ->statePath('data');
    }

    // public function extraService_form(Schema $schema): Schema
    // {
    //     return $schema
    //         ->components([ // In v4, we use ->components([]) instead of ->schema([])
    //             Select::make('extra_service_id')
    //                 ->hiddenLabel()
    //                 ->placeholder('Select Extra Service')
    //                 // Using a query makes it more efficient
    //                 ->options(ExtraService::all()->pluck('title', 'id'))
    //                 ->searchable()
    //                 ->preload()
    //                 ->multiple()
    //                 ->live()
    //                 ->native(false), // Forces the nice UI even on mobile
    //         ])
    //         ->statePath('data');

    // }


    // public function updatedSelectedTherapistTypeId($value)
    // {
    //     // This method runs automatically when the select changes
    //     // $value is the currently selected id
    //     info("Selected therapist type ID: " . $value);

    //     // Example: get the full model
    //     $selectedType = TherapistType::find($value);
    //     info("Selected therapist title: " . ($selectedType->title ?? 'None'));
    // }


    public function switchTherapistAndTherapistType()
    {
        $room = DailyRoomRecord::find($this->selectedRoom->id);
        $room->update([
            'therapist_id' => $this->data['therapist_id'] ?? $room->therapist_id,
            'service_type' => TherapistType::find($this->selectedTherapistTypeId)->id ?? $room->service_type
        ]);
    }

    public function checkBill()
    {
        // dump($this->checkRoomIds);

        return redirect()->route('filament.admin.pages.check-bill', [
            'bill_for' => implode(',', $this->checkRoomIds)
        ]);
    }


    // public function selectCell(int $roomId, int $slotId): void
    // {

    //     $this->selectedRoomId = $roomId;
    //     $this->selectedSlotId = $slotId;

    //     $this->selectedRoomName = Room::find($roomId)->name;
    //     $this->selectedTherapistName = $this->getThapistName($roomId, $slotId);
    //     $this->selectedTherapistType = $this->getTherapistType($roomId, $slotId);
    //     $this->selectedExtraServicesList = $this->getRoomExtraServices($roomId, $slotId);

    //     $this->selectedStartTime = TimeSlot::find($slotId)->start_time;
    //     $this->selectedEndTime =  TimeSlot::find($slotId)->end_time;
    //     $this->selectedTimeSection = $this->selectedStartTime . ' - ' . $this->selectedEndTime;

    //     $this->isExistingRecord = DailyRoomRecord::where('record_date', $this->date)
    //     ->where('room_id', $this->selectedRoomId)
    //     ->where('time_slot_id', $this->selectedSlotId)
    //     ->first();

    //     $this->sameTimeSlotForTherapist = $this->getSameTimeSlotTherapist($roomId, $slotId);

    //     // $this->mountAction('cellModal'); {{ to show up the modal box }}
    //     //  $this->reset(['selectedRoomId', 'selectedSlotId', 'selectedTherapistId']);
    // }

    // public function assign(): void
    // {
    //     $dailyRoomRecord = DailyRoomRecord::where('record_date', $this->date)
    //                                         ->where('room_id', $this->selectedRoomId)
    //                                         ->where('time_slot_id', $this->selectedSlotId)
    //                                         ->whereNotNull('invoice_id')
    //                                         ->first();

    //     if ($dailyRoomRecord) {
    //         Notification::make()
    //         ->title('Billing Error')
    //         ->body('This room has already been billed.')
    //         ->danger()
    //         ->send();

    //         return;
    //     }

    //     $this->selectedTherapistId = $this->data['therapist_id'] ?? null;
    //     $room = Room::find($this->selectedRoomId);

    //     $therapistType = TherapistType::find($this->selectedType);

    //     $alreadyAssigned = DailyRoomRecord::where([
    //         'record_date' => $this->date,
    //         'time_slot_id' => $this->selectedSlotId,
    //         'therapist_id' => $this->selectedTherapistId,
    //          'service_type' => $this->selectedType,
    //          'service_type_price' => $therapistType ? $therapistType->price : 0,
    //     ])->first();

    //     if($alreadyAssigned) {
    //         $this->notifyError(
    //                 'Error: Assignment Error',
    //                 'You have already selected a therapist for this time slot. Please select a different therapist.'
    //             );
    //             return;

    //     }

    //     DailyRoomRecord::updateOrCreate(
    //         [
    //             'record_date' => $this->date,
    //             'room_id' => $this->selectedRoomId,
    //             'time_slot_id' => $this->selectedSlotId,
    //         ],
    //         [
    //             'therapist_id' => $this->selectedTherapistId,
    //             'service_type' => $this->selectedType,
    //             'price' => $room ? $room->price : 0,
    //             'service_type_price' => $therapistType ? $therapistType->price : 0,
    //         ]
    //     );

    //     $this->reset(['selectedRoomId', 'selectedSlotId']);

    //     $this->data['therapist_id'] = null;
    //     $this->therapist_form->fill();

    // }

    // public function removeTherapist(): void
    // {
    //     $therapist = DailyRoomRecord::where([
    //         'record_date' => $this->date,
    //         'room_id' => $this->selectedRoomId,
    //         'time_slot_id' => $this->selectedSlotId
    //     ])->first();

    //     if($therapist->saleProducts->count() > 0) {
    //         $this->notifyError(
    //             'Error: Remove Error',
    //             'You cannot unassign a therapist with products. Please remove the products first.'
    //         );return;
    //     }else{
    //         $therapist->delete();
    //         $this->notifySuccess(
    //             'Success: Remove Success',
    //             'Unassigned successfully.'
    //         );return;
    //     }
    // }

    public function addProduct(): void
    {
        $dailyRoomRecord = DailyRoomRecord::where('record_date', $this->date)->where('id', $this->selectedRoom->id)
                                            ->whereNull('invoice_id')
                                            ->first();

        $this->selectedProductId = $this->data['product_id'] ?? null;

        if (!$dailyRoomRecord) {
              $this->notifyError('Error', 'This room has already been billed.');
        }

        $productSale = ProductSale::where('daily_room_record_id', $dailyRoomRecord->id)
                                ->where('product_id', $this->selectedProductId)->first();

        $product = Product::where('id', $this->selectedProductId)->first();


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
        $this->getSalesForDailyRoomRecord();
        $this->notifySuccess(
            'Success: Add Success',
            'Product added successfully.'
        );

        $this->reset(['selectedProductId']);
    }

    public function removeProduct($saleproduct): void
    {
        $productSale = ProductSale::where('id', $saleproduct)->delete();

        $this->getSalesForDailyRoomRecord();

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

        $this->getSalesForDailyRoomRecord();

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

        $this->getSalesForDailyRoomRecord();

        $this->notifySuccess(
            'Success: Add Success',
            'Product quantity added successfully.'
        );
        return;
    }


    public function updateExtraService()
    {

        $alreadyExists = ExtraServiceSale::where('extra_service_id', $this->selectedExtraServiceId)
                        ->where('daily_room_record_id', $this->selectedRoom->id)->first();

        if(!$alreadyExists){

            $dailyRoom = DailyRoomRecord::where('id', $this->selectedRoom->id)->first();

            $price = ExtraService::where('id', $this->selectedExtraServiceId)->first()->price;

            ExtraServiceSale::create([
                'extra_service_id' => $this->selectedExtraServiceId,
                'daily_room_record_id' => $dailyRoom->id,
                'branch_id' => 1,
                'unit_price' => $price,
                'total_price' => $price
            ]);
            $this->selectedExtraServicesList = $this->getRoomExtraServices($this->selectedRoom->id);

            $this->getSalesForDailyRoomRecord();

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

    public function removeService($serviceId): void
    {


        $productSale = ExtraServiceSale::where('id', $serviceId)->delete();
        $this->selectedExtraServicesList = $this->getRoomExtraServices($this->selectedRoom->id);
        $this->getSalesForDailyRoomRecord();
        $this->notifySuccess(
            'Success: Remove Success',
            'Extra service removed successfully.'
        );
    }


    // public function removeExtraService($saleproduct): void
    // {
    //     $productSale = ExtraServiceSale::where('id', $saleproduct)->delete();
    //     $this->selectedExtraServicesList = $this->getRoomExtraServices($this->selectedRoomId, $this->selectedSlotId);
    //     $this->notifySuccess(
    //         'Success: Remove Success',
    //         'Extra service removed successfully.'
    //     );
    // }

    // public function getCanAddProductProperty() : bool {
    //     $dailyRoomRecord = DailyRoomRecord::where('record_date', $this->date)
    //                         ->where('room_id', $this->selectedRoomId)
    //                         ->where('time_slot_id', $this->selectedSlotId)
    //                         ->first();

    //     $result = $dailyRoomRecord ? 0 : 1;
    //     return $result;
    // }

    // public function getThapistName($roomId, $slotId): string
    // {
    //     $schedule = DailyRoomRecord::where([
    //         'record_date' => $this->date,
    //         'room_id' => $roomId,
    //         'time_slot_id' => $slotId,
    //     ])->with('therapist')->first();

    //     return $schedule?->therapist?->name ?? "";
    // }

    // public function getTherapistType($roomId, $slotId): string
    // {
    //     $schedule = DailyRoomRecord::where([
    //         'record_date' => $this->date,
    //         'room_id' => $roomId,
    //         'time_slot_id' => $slotId,
    //     ])->with('therapistType')->first();

    //     return $schedule?->therapistType?->title ?? "";
    // }

    public function getRoomExtraServices($roomId): array
    {
        $schedule = DailyRoomRecord::where([
            'record_date' => $this->date,
            'room_id' => $roomId,
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

    // public function checkBill($roomId, $slotId): bool
    // {
    //     $billCheck = DailyRoomRecord::where([
    //         'record_date' => $this->date,
    //         'room_id' => $roomId,
    //         'time_slot_id' => $slotId,
    //     ])->whereNotNull('invoice_id')->count();

    //     return $billCheck ? true : false;
    // }

    // public function getDailyRoomRecordId($roomId, $slotId): string
    // {
    //     $schedule = DailyRoomRecord::where([
    //         'record_date' => $this->date,
    //         'room_id' => $roomId,
    //         'time_slot_id' => $slotId,
    //     ])->select('id')->first();

    //     return $schedule?->id ?? "";
    // }

    // public function getSelectedRoomRecordProductsProperty(): ? Array
    // {
    //     if ($this->selectedRoomId && $this->selectedSlotId) {

    //         $dailyRoomRecord = DailyRoomRecord::where([
    //             'record_date' => $this->date,
    //             'room_id' => $this->selectedRoomId,
    //             'time_slot_id' => $this->selectedSlotId,
    //         ])->first();

    //         if (!$dailyRoomRecord) {
    //             return [];
    //         }

    //         $productSale = ProductSale::with('product')->where('daily_room_record_id', $dailyRoomRecord->id)->get();

    //         return [
    //             'dailyRoomRecord' => $dailyRoomRecord,
    //             'productSales' => $productSale,
    //             'productSaleTotal' => $productSale->sum('total_price'),
    //         ];
    //     }

    //     return [];
    // }

    // public function goToCheckBill()
    // {
    //     // if (! $this->selectedRoomId || ! $this->selectedSlotId) {
    //     //     return;
    //     // }
    //     return redirect()->route('filament.admin.pages.check-bill', [
    //         'bill_for' => implode(',', $this->selectedRoomIdsForBill)
    //     ]);
    // }



    // public function getTherapistTypes()
    // {
    //     return TherapistType::all();
    // }



    public function getSalesForDailyRoomRecord()
    {

        $saleProducts = ProductSale::where('daily_room_record_id', $this->selectedRoomId)->with('product')->get();
        $saleExtraServices = ExtraServiceSale::where('daily_room_record_id', $this->selectedRoomId)->with('extraService')->get();

        $data = [
            'saleProducts' => $saleProducts,
            'saleProductsTotal' => $saleProducts->sum('total_price'),
            'saleExtraServices' => $saleExtraServices,
            'saleExtraServicesTotal' => $saleExtraServices->sum('total_price'),
            'saleTotal' => $saleProducts->sum('total_price') + $saleExtraServices->sum('total_price'),
        ];
        $this->extraServiceAndProductSales = $data;

    }







    // public function getSameTimeSlotTherapist($roomId, $slotId)
    // {
    //     return DailyRoomRecord::where(['time_slot_id' => $this->selectedSlotId])
    //     ->where('record_date', $this->date)->with('therapist')->pluck('therapist_id');
    // }


    // public function notifyError(string $type, string $message): void
    // {
    //     Notification::make()
    //             ->title($type)
    //             ->body($message)
    //             ->danger() // Makes the notification red
    //             ->persistent() // Stays on screen until they click it
    //             ->send();
    // }
    // public function notifySuccess(string $type, string $message): void
    // {
    //     Notification::make()
    //             ->title($type)
    //             ->body($message)
    //             ->success() // Makes the notification red
    //             ->persistent() // Stays on screen until they click it
    //             ->send();
    // }



    public function notifyError(string $type, string $message): void
    {
        Notification::make()
                ->title($type)
                ->body($message)
                ->danger() // Makes the notification red
                ->persistent() // Stays on screen until they click it
                ->duration(500)
                ->send();
    }
    public function notifySuccess(string $type, string $message): void
    {
        Notification::make()
                ->title($type)
                ->body($message)
                ->success() // Makes the notification red
                ->persistent() // Stays on screen until they click it
                ->duration(500)
                ->send();
    }




}
