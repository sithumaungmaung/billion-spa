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
    // protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-text'; // Replace 'heroicon-o-document-text' with your desired icon
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-building-office'; // Replace 'heroicon-o-document-text' with your desired icon


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
    public ?int $selectedExtraServiceQty = 1;
    public ?array $selectedExtraServicesList = [];

    // Select for time
    public $selectedStartTime = '';
    public $selectedEndTime = '';
    // public $selectedRoomSections = 0;


    // Default
    public $activeRooms;
    public $avaliableRooms;
    public $therapistTypes;
    public $extraServices;

    //  Detail

    public $extraServiceAndProductSales = [];


    public ?array $data = [];

    public function mount(): void
    {
        $this->date = now()->toDateString();
        $this->avaliableRooms = $this->avaliavleRooms();
        $this->activeRooms = $this->activeRooms();
        $this->rooms =  $this->activeRooms;

        $this->therapistTypes = TherapistType::get();
        $this->extraServices = ExtraService::get();

        $this->therapist_form->fill();
        $this->room_form->fill();
        $this->product_form->fill();


    }



    public function selectRoom(int $roomId)
    {
        $selectedRoom = DailyRoomRecord::with('room', 'therapist', 'therapistType', 'saleProducts')->where('id', $roomId)->first();
        $this->selectedRoom = $selectedRoom;
        $this->selectedRoomId = $selectedRoom->id;
        // $this->rooms = $this->selectedRoom ? [$selectedRoom] : $this->activeRooms;
        $this->selectedRoom = DailyRoomRecord::where('id', $selectedRoom->id)->first();

        if(!$this->data['room_id']){
            $this->rooms =  $this->activeRooms();
        }

        $this->data['therapist_id'] = $selectedRoom->therapist->id;

        $this->selectedStartTime = $selectedRoom->start_time;
        $this->selectedEndTime = $selectedRoom->end_time;


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

                ->where('start_time' , '<=', now()->format('Y-m-d\TH:i'))
                ->where('end_time', '>=', now()->format('Y-m-d\TH:i'))->get();

        $originalRooms = Room::whereNotIn('id', $activeRooms->pluck('room_id'))->get();




    }

    public function activeRooms ()
    {
        $roomRecords = DailyRoomRecord::where('record_date', $this->date)
                        ->with('room', 'therapist', 'therapistType')
                        ->whereNull('invoice_id')
                        ->get();


        return $roomRecords;
    }

    //  FORMS =============
    public function therapist_form(Schema $schema): Schema
    {

        return $schema
            ->components([
                Select::make('therapist_id')
                    ->hiddenLabel()
                    ->placeholder('Select Therapist')
                    // Using a query makes it more efficient
                    ->options(fn () => $this->getTherapistOptions())
                    ->disableOptionWhen(fn ($value) =>
                        $this->selectedRoom?->therapist_id === $value
                    )
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
                        ->where('start_time' , '<=', now()->format('Y-m-d\TH:i'))
                        ->where('end_time', '>=', now()->format('Y-m-d\TH:i'))->get();

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
                            // Room Record Schedule // Not Physical Room
                            $this->selectedRoomId = DailyRoomRecord::where('room_id', $state)->first()->id;
                            $this->selectedRoom = DailyRoomRecord::where('room_id', $state)->first();
                            $this->getSalesForDailyRoomRecord();

                            $this->rooms =  DailyRoomRecord::whereIn('room_id', [$state])->get();
                            $this->data['therapist_id'] = $this->selectedRoom->therapist->id;

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


    public function switchTherapistAndTherapistType()
    {
        $room = DailyRoomRecord::find($this->selectedRoom->id);
        $room->update([
            'therapist_id' => $this->data['therapist_id'] ?? $room->therapist_id,
            'service_type' => TherapistType::find($this->selectedTherapistTypeId)->id ?? $room->service_type,
            'service_type_price' => TherapistType::find($this->selectedTherapistTypeId)->price ?? $room->service_type_price
        ]);
        $this->rooms =  $this->activeRooms();
    }

    public function checkBill()
    {
        $url = route('filament.admin.pages.check-bill', [
            'bill_for' => implode(',', $this->checkRoomIds)
        ]);

        $this->dispatch('check.bill', url: $url);
    }


    public function unassigRoom()
    {
        $room = DailyRoomRecord::where('id', $this->selectedRoom->id)
        ->with('therapist', 'extraServices','therapistType')->whereNull('invoice_id')->first();

        if($room->saleProducts()->count() > 0){
            $this->notifyError(
                'Error', 'You cannot unassign a therapist with products. Please remove the products first.'
            );
            return;
        }

        if($room->extraServices()->count() > 0){
            $this->notifyError(
                'Error', 'You cannot unassign a therapist with extra services. Please remove the extra services first.'
            );
            return;
        }


        $room->delete();

        $this->selectedRoom = null;

        $this->activeRooms = $this->activeRooms();
        $this->rooms =  $this->activeRooms;


        $this->notifySuccess(
            'Success', 'Unassigned successfully.'
        );
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
                'quantity' => $this->selectedExtraServiceQty,
                'unit_price' => $price,
                'total_price' => $price * $this->selectedExtraServiceQty
            ]);
            $this->selectedExtraServicesList = $this->getRoomExtraServices($this->selectedRoom->id);

            $this->getSalesForDailyRoomRecord();

        }else{

            $alreadyExists->update([
                'total_price' => $alreadyExists->total_price + $alreadyExists->unit_price,
                'unit_price' => $alreadyExists->unit_price,
                'quantity' => $alreadyExists->quantity + $this->selectedExtraServiceQty
            ]);

            $this->getSalesForDailyRoomRecord();
        }

        $this->notifySuccess(
            'Success: Add Success',
            'Extra service added successfully.'
        );
    }

    public function removeService($serviceId): void
    {

        $extraService = ExtraServiceSale::where('id', $serviceId)->delete();
        $this->selectedExtraServicesList = $this->getRoomExtraServices($this->selectedRoom->id);
        $this->getSalesForDailyRoomRecord();
        $this->notifySuccess(
            'Success: Remove Success',
            'Extra service removed successfully.'
        );
    }


    public function reduceExtraService($saleExtraServiceId)
    {
        $productSale = ExtraServiceSale::where('id', $saleExtraServiceId)->first();
        $totalQty = $productSale->quantity - 1;
        $productSale->update([
            'quantity' => $totalQty,
            'total_price' => $totalQty * $productSale->unit_price
        ]);

        $this->getSalesForDailyRoomRecord();

        $this->notifySuccess(
            'Success: Reduce Success',
            'Extra Service quantity reduced successfully.'
        );
        return;
    }

    public function addMoreExtraService($saleExtraServiceId)
    {
        $productSale = ExtraServiceSale::where('id', $saleExtraServiceId)->first();
        $toalQty = $productSale->quantity + 1;
        $productSale->update([
            'quantity' => $toalQty,
            'total_price' => $toalQty * $productSale->unit_price
        ]);

        $this->getSalesForDailyRoomRecord();

        $this->notifySuccess(
            'Success: Add Success',
            'Extra Service quantity added successfully.'
        );
        return;
    }




    public function updateTime()
    {
        $dailyRoomRecord = DailyRoomRecord::where('id', $this->selectedRoom->id)->first();

        $convertedStartTime = Carbon::parse($this->selectedStartTime)->format('Y-m-d\TH:i');
        $convertedEndTime = Carbon::parse($this->selectedEndTime)->format('Y-m-d\TH:i');


        if($convertedEndTime <= $dailyRoomRecord->start_time ||
            $convertedEndTime <= $convertedStartTime ||

            $convertedStartTime >= $convertedEndTime
        ) {
            $this->notifyError('Error', 'End time must be greater than start time');
            $this->selectedStartTime = $dailyRoomRecord->start_time;
            $this->selectedEndTime = $dailyRoomRecord->end_time;
            return;
        }

        $dailyRoomRecord->update([
            'start_time' => $this->selectedStartTime ? $convertedStartTime : $dailyRoomRecord->start_time,
            'end_time' => $this->selectedEndTime ? $convertedEndTime : $dailyRoomRecord->end_time
        ]);

        $this->rooms =  $this->activeRooms();

        $this->notifySuccess(
            'Success: Update Success',
            'Time updated successfully.'
        );

    }


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
