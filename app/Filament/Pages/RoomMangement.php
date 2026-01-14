<?php

namespace App\Filament\Pages;

use BackedEnum;
use Carbon\Carbon;
use App\Models\Room;
use App\Models\Product;
use App\Models\TimeSlot;
use Filament\Pages\Page;
use App\Models\Therapist;
use App\Models\ProductSale;
use Ramsey\Uuid\Type\Integer;

use App\Models\DailyRoomRecord;
use function PHPSTORM_META\map;

use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

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
    public ?array $selectedRoomIdsForVoucher = [];
    public ?int $selectedSlotId = null;
    public ?int $selectedTherapistId = null;
    public ?int $selectedProductId = null;
    public ?int $searchRoomId = 0;
    public ?int $selectedType = 1;

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

        //  $this->reset(['selectedRoomId', 'selectedSlotId', 'selectedTherapistId']);
    }

    public function assign(): void
    {

        $this->selectedTherapistId = $this->data['therapist_id'] ?? null;

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
        // dump($this->getFreeTherapists());
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


}
