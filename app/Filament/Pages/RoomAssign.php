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
    public $selectedTherapistTypeId = 1;

    // -------------------------------- //

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData()
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
                        ->where('start_time' , '<', $this->endTime ?? now()->format('Y-m-d\TH:i'))
                        ->where('end_time', '>', $this->startTime ?? now()->format('Y-m-d\TH:i'))
                        ->get();
       return  Therapist::whereNotIn('id', $roomRecords->pluck('therapist_id'))->get();
    }


    public function avaliavleRooms ()
    {
        $roomRecords = DailyRoomRecord::where('record_date', $this->date)
                        ->where('start_time' , '<', $this->endTime ?? now()->format('Y-m-d\TH:i'))
                        ->where('end_time', '>', $this->startTime ?? now()->format('Y-m-d\TH:i'))
                        ->get();

        return Room::whereNotIn('id', $roomRecords->pluck('room_id'))->get();
    }


    public function updatedStartTime($startTime)
    {
        $this->avaliableTherapists = $this->getFreeTherapists();
        $this->avaliableRooms = $this->avaliavleRooms();

    }

    public function updatedEndTime($endTime)
    {
        $this->avaliableTherapists = $this->getFreeTherapists();
        $this->avaliableRooms = $this->avaliavleRooms();
        // dump($this->avaliableTherapists->toArray());
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
                            ->first();


        if ($alreadyAssigned) {
            $this->notifyError('Error', 'Room is already assigned');
            return;
        }


         if(!in_array((int)$this->selectedTherapistId, $this->avaliableTherapists->pluck('id')->toArray())) {
            $this->notifyError('Error', 'Therapist is already assigned');
            return;
        }

        $record = DailyRoomRecord::create([
            'record_date' => $this->date,
            'room_id' => $this->selectedRoomId,
            'therapist_id' => $this->selectedTherapistId,
            'service_type' => $this->selectedTherapistTypeId,
            'start_time' => Carbon::parse($this->startTime),
            'end_time' => Carbon::parse($this->endTime),
            'room_price' => Room::find($this->selectedRoomId)->price,
            'service_type_price' => TherapistType::find($this->selectedTherapistTypeId)->price
        ]);

        if ($record) {
            $this->avaliableRooms = $this->avaliavleRooms();
            $this->avaliableTherapists = $this->getFreeTherapists();
            $this->resetForm();
            $this->notifySuccess('Success', 'Room assigned successfully');
        }

    }

    public function resetForm()
    {
        $this->startTime = null;
        $this->endTime = null;
        $this->selectedRoomId = null;
        $this->selectedTherapistId = null;
        $this->selectedTherapistTypeId = null;

    }

    //  Notification

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