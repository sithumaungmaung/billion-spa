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

use Filament\Schemas\Schema;
use Ramsey\Uuid\Type\Integer;

use App\Models\DailyRoomRecord;
use function PHPSTORM_META\map;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
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
    public ?int $selectedType = 1;

    public string $selectedRoomName = '';
    public string $selectedTherapistName = '';
    public string $selectedTimeSection = '';

    public ?string $selectedProductSaleId = null;

    public ?array $data = [];

    public function mount(): void
    {
        $this->date = now()->toDateString();
        $this->rooms = Room::get();
        $this->timeSlots = TimeSlot::get();
        $this->therapists = $this->getFreeTherapists();


        $this->form->fill();

        $this->products = Product::get();



    }


    public function selectCell(int $roomId, int $slotId): void
    {

        $this->selectedRoomId = $roomId;
        $this->selectedSlotId = $slotId;

        $this->selectedRoomName = Room::find($roomId)->name;
        $this->selectedTherapistName = $this->getThapistName($roomId, $slotId);
        $this->selectedTimeSection = TimeSlot::find($slotId)->start_time . ' - ' . TimeSlot::find($slotId)->end_time;


        // dump($this->data);

        //  $this->reset(['selectedRoomId', 'selectedSlotId', 'selectedTherapistId']);
    }

    public function assign(): void
    {

        $this->selectedTherapistId = $this->data['therapist_id'] ?? null;

        $alreadyAssigned = DailyRoomRecord::where([
            'record_date' => $this->date,
            'time_slot_id' => $this->selectedSlotId,
            'therapist_id' => $this->selectedTherapistId
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
                'service_type' => $this->selectedType
            ]
        );
        $this->reset(['selectedRoomId', 'selectedSlotId', 'selectedTherapistId']);

        $this->data['therapist_id'] = null;
        $this->form->fill();

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
                                            ->first();

        $productSale = ProductSale::where('daily_room_record_id', $dailyRoomRecord->id)
                                ->where('product_id', $this->selectedProductId)->first();

        $product = Product::where('id', $this->selectedProductId)->first();

        // dump($productSale ?? $productSale->toArray());

        if(!$product) {
            return;
        }

        if ($productSale) {
            $productSale->update([
                'quantity' => $productSale->quantity + 1,
                'unit_price' => $product->price,
                'quantity' => $productSale->quantity + 1,
                'branch_id' => 1,
                'total_price' => $productSale->quantity * $product->price
            ]);
        } else {
            ProductSale::create([
                'daily_room_record_id' => $dailyRoomRecord->id,
                'product_id' => $this->selectedProductId,
                'unit_price' => $product->price,
                'quantity' => 1,
                'branch_id' => 1,
                'total_price' => $product->price
            ]);
        }

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
        $productSale->update([
            'quantity' => $productSale->quantity - 1,
            'total_price' => $productSale->quantity * $productSale->unit_price
        ]);

        $this->notifySuccess(
            'Success: Reduce Success',
            'Product quantity reduced successfully.'
        );
        return;
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

    public function getDailyRoomRecordId($roomId, $slotId): string
    {
        $schedule = DailyRoomRecord::where([
            'record_date' => $this->date,
            'room_id' => $roomId,
            'time_slot_id' => $slotId,
        ])->select('id')->first();

        return $schedule?->id ?? null;
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
                'productSales' => $productSale
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
            ->pluck('therapist_id')
            ->unique()
            ->toArray();

        return $allStaffs->whereNotIn('id', $busyStaffIds)->values();

    }


    public function form(Schema $schema): Schema
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


}
