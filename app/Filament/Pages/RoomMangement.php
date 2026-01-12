<?php

namespace App\Filament\Pages;

use App\Models\DailyRoomRecord;
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

    public ?int $selectedRoomId = null;
    public ?int $selectedSlotId = null;
    public ?int $selectedTherapistId = null;

    public function mount(): void
    {
        $this->date = now()->toDateString();
        $this->rooms = Room::get();
        $this->timeSlots = TimeSlot::get();
        $this->therapists = Therapist::get();
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
                'therapist_id' => $this->selectedStaffId,
            ]
        );

        $this->reset(['selectedRoomId', 'selectedSlotId', 'selectedStaffId']);
    }

    public function getThapistName($roomId, $slotId): string
    {
        $schedule = DailyRoomRecord::where([
            'record_date' => $this->date,
            'room_id' => $roomId,
            'time_slot_id' => $slotId,
        ])->with('therapist')->first();

        return $schedule?->therapist?->name ?? '-';
    }
}