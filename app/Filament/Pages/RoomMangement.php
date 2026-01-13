<?php

namespace App\Filament\Pages;

use BackedEnum;
use App\Models\Room;
use App\Models\Product;
use App\Models\TimeSlot;
use Filament\Pages\Page;
use App\Models\Therapist;
use App\Models\ProductSale;
use App\Models\DailyRoomRecord;
use Ramsey\Uuid\Type\Integer;

use function PHPSTORM_META\map;

class RoomMangement extends Page
{
    protected string $view = 'filament.pages.room-mangement';
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-text'; // Replace 'heroicon-o-document-text' with your desired icon

    public string $date;
    public $rooms;
    public $timeSlots;
    public $therapists;
    public $products;

    public ?int $selectedRoomId = null;
    public ?array $selectedRoomIdsForVoucher = [];
    public ?int $selectedSlotId = null;
    public ?int $selectedTherapistId = null;
    public ?int $selectedProductId = null;
    public ?int $searchRoomId = 0;


    public function mount(): void
    {
        $this->date = now()->toDateString();
        $this->rooms = Room::get();
        $this->timeSlots = TimeSlot::get();
        $this->therapists = Therapist::get();
        $this->products = Product::get();
    }

    public function selectCell(int $roomId, int $slotId): void
    {
        $this->selectedRoomId = $roomId;
        $this->selectedSlotId = $slotId;
    }

    public function assign(): void
    {
        DailyRoomRecord::updateOrCreate(
            [
                'record_date' => $this->date,
                'room_id' => $this->selectedRoomId,
                'time_slot_id' => $this->selectedSlotId,
            ],
            [
                'therapist_id' => $this->selectedTherapistId,
            ]
        );

        $this->reset(['selectedRoomId', 'selectedSlotId', 'selectedTherapistId']);
    }

    public function addProduct(): void
    { 
        $dailyRoomRecord = DailyRoomRecord::where('record_date', $this->date)->where('room_id', $this->selectedRoomId)
                                            ->where('time_slot_id', $this->selectedSlotId)
                                            ->first();
        
        $productSale = ProductSale::where('daily_room_record_id', $dailyRoomRecord->id)
                                ->where('product_id', $this->selectedProductId)->first();

        $product = Product::where('id', $this->selectedProductId)->first();

        if(!$product) {
            return;
        }

        if ($productSale) {
            $productSale->update([
                'quantity' => $productSale->quantity + 1,
                'unit_price' => $product->price,
                'quantity' => 1,
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

        $this->reset(['selectedRoomId', 'selectedSlotId', 'selectedTherapistId']);
    }

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
}
