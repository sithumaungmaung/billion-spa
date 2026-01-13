<?php

namespace App\Filament\Pages;

use App\Models\DailyRoomRecord;
use App\Models\Product;
use App\Models\Room;
use App\Models\Therapist;
use App\Models\TimeSlot;
use Filament\Pages\Page;
use BackedEnum;

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
        $cost = 0;

        ProductSale::updateOrCreate(
            [
                'record_date' => $this->date,
                'room_id' => $this->selectedRoomId,
                'time_slot_id' => $this->selectedSlotId,
            ],
            [
                'product_id' => $this->product,
                'price' => $cost,
            ]
        );

        $this->reset(['selectedRoomId', 'selectedSlotId', 'selectedTherapistId']);
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
