<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            Daily Room Service Schedule
        </x-slot>

        <x-slot name="description">
            <input type="date" wire:model.live="date" name="date">
            @if ($date === now()->toDateString())
                <span class="text-green-600 font-medium">Today</span>
            @endif
        </x-slot>


        <x-filament::input.wrapper label="Select Therapist">
            <x-filament::input.select wire:model.live="searchRoomId">
                <option value="0">Find Room</option>
                @foreach ($rooms as $room)
                    <option value="{{ $room->id }}">{{ $room->name }}</option>
                @endforeach
            </x-filament::input.select>

        </x-filament::input.wrapper> <br>

        <div class="overflow-x-auto border border-gray-200 dark:border-white/10 rounded-lg">

            <table class="w-full text-sm text-left table-fixed">
                <thead class="bg-gray-50 dark:bg-white/5 uppercase text-xs">
                    <tr>
                        <th class="p-3 border-b dark:border-white/10 w-[100px]">Room</th>
                        @foreach ($timeSlots as $slot)
                            <th class="p-3 border-b border-l dark:border-white/10 text-center w-[200px]">
                                {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y dark:divide-white/10">
                    @foreach ($rooms as $room)
                        <tr @if (!($room->id == $searchRoomId || $searchRoomId == 0)) style="display: none;" @endif>
                            <td class="p-3 font-bold bg-gray-50/30 dark:bg-white/5">
                                {{ $room->name }}
                            </td>
                            @foreach ($timeSlots as $slot)
                                <td wire:click="selectCell({{ $room->id }}, {{ $slot->id }})"
                                    @class([
                                        'p-3 text-center cursor-pointer border-l dark:border-white/10 transition',
                                        'bg-primary-500/20' =>
                                            $selectedRoomId == $room->id && $selectedSlotId == $slot->id,
                                        'hover:bg-primary-500/5' => !(
                                            $selectedRoomId == $room->id && $selectedSlotId == $slot->id
                                        ),
                                    ])>

                                    {{ $this->getThapistName($room->id, $slot->id) ?: '—' }} <br> <br>

                                    @if ($this->getThapistName($room->id, $slot->id))
                                        <input type="checkbox" wire:model.live="selectedTherapistIds"
                                            value="{{ $room->id . '-' . $slot->id }}">
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>

    <!-- {{ $selectedRoomId . '-' . $selectedSlotId . '-' . $selectedTherapistId }} -->

    <div class="flex items-end gap-4">
        <div class="flex-1">
            <x-filament::input.wrapper label="Select Therapist">
                <x-filament::input.select wire:model.live="selectedTherapistId">
                    <option value="">Choose Staff</option>
                    @foreach ($therapists as $person)
                        <option value="{{ $person->id }}">{{ $person->name }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </div>
        <x-filament::button wire:click="assign" size="lg">
            Assign
        </x-filament::button>

        <div class="flex-1">
            <x-filament::input.wrapper label="Select Therapist">
                <x-filament::input.select wire:model.live="selectedTherapistId">
                    <option value="">Choose Product</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </div>
        <x-filament::button wire:click="addProduct" size="lg">
            Add
        </x-filament::button>
    </div>

</x-filament-panels::page>
