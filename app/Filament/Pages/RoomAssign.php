<?php

namespace App\Filament\Pages;

use BackedEnum;
use Carbon\Carbon;
use App\Models\Room;
use Filament\Pages\Page;
use App\Models\Therapist;
use App\Models\TherapistType;
use App\Models\DailyRoomRecord;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;

class RoomAssign extends Page implements HasForms
{
    protected string $view = 'filament.pages.room-assign';

    protected static ?string $navigationLabel = 'Room Assign Page';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';

    // -------------------------------- //

    public $date;
    public $rooms;
    public $avaliableRooms;
    public $avaliableTherapists;
    public $therapistTypes;

    // ---------------- Selector ----------------- //
    public $startTime;
    public $endTime;

    public $selectedRoomId;
    public $selectedTherapistId;
    public $selectedTherapistTypeId;

    // -------------------------------- //

    public function mount(): void
    {
        $this->date = now()->toDateString();
        $this->rooms = Room::get();
        $this->avaliableRooms = $this->avaliavleRooms();
        $this->avaliableTherapists = $this->getFreeTherapists();
        $this->therapistTypes = TherapistType::get();
    }


    public function getFreeTherapists()
    {
        $roomRecords = DailyRoomRecord::where('record_date', $this->date)
                        ->where('start_time' , '<=', now()->format('H:i:s'))
                        ->where('end_time', '>=', now()->format('H:i:s'))->get();
        return Therapist::whereNotIn('id', $roomRecords->pluck('therapist_id'))->get();
    }


    public function avaliavleRooms ()
    {
        $roomRecords = DailyRoomRecord::where('record_date', $this->date)
                        ->where('start_time' , '<', $this->endTime ?? now()->format('H:i:s'))
                        ->where('end_time', '>', $this->startTime ?? now()->format('H:i:s'))
                        ->get();
        return Room::whereNotIn('id', $roomRecords->pluck('room_id'))->get();
    }


    public function updatedStartTime($startTime)
    {
        $this->avaliableRooms = $this->avaliavleRooms();
    }

    public function updatedEndTime($startTime)
    {
        $this->avaliableRooms = $this->avaliavleRooms();
    }


    public function createRoomAssign()
    {

        if($this->endTime <= $this->startTime) {
            $this->notifyError('Error', 'End time must be greater than start time');
            return;
        }

        $alreadyAssigned = DailyRoomRecord::where('record_date', $this->date)
                            ->where('room_id', $this->selectedRoomId)
                            ->where('start_time' , '<=', $this->startTime)
                            ->where('end_time', '>=', $this->endTime)
                            ->where('therapist_id', $this->selectedTherapistId)
                            ->first();

        if ($alreadyAssigned) {
            $this->notifyError('Error', 'Room or Therapist already assigned');
            return;
        }
        $record = DailyRoomRecord::create([
            'record_date' => $this->date,
            'room_id' => $this->selectedRoomId,
            'therapist_id' => $this->selectedTherapistId,
            'service_type' => $this->selectedTherapistTypeId,
            'start_time' => Carbon::parse($this->startTime)->format('H:i:s'),
            'end_time' => Carbon::parse($this->endTime)->format('H:i:s'),
            'room_price' => Room::find($this->selectedRoomId)->price,
            'service_type_price' => TherapistType::find($this->selectedTherapistTypeId)->price
        ]);

        if ($record) {
            $this->avaliableRooms = $this->avaliavleRooms();
            $this->avaliableTherapists = $this->getFreeTherapists();
            $this->notifySuccess('Success', 'Room assigned successfully');
        }

    }


    //  Notification

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
