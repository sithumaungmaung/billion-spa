<x-filament::page>

    <h2 class="text-xl font-bold mb-4">
        Daily Room Service Schedule
    </h2>

    <p class="mb-4 text-gray-400">
        {{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}
    </p>

    <div class="overflow-x-auto">
        <table class="w-full border border-gray-700 text-sm">
            <thead>
                <tr class="bg-gray-800">
                    <th class="border p-2">Room</th>
                    @foreach ($timeSlots as $slot)
                        <th class="border p-2">
                            {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}
                        </th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                @foreach ($rooms as $room)
                    <tr>
                        <td class="border p-2 font-semibold">
                            {{ $room->name }}
                        </td>

                        @foreach ($timeSlots as $slot)
                            <td
                                wire:click="selectCell({{ $room->id }}, {{ $slot->id }})"
                                class="border p-2 text-center cursor-pointer hover:bg-primary-500/10"
                            >
                                {{ $this->getThapistName($room->id, $slot->id) }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Assign Section --}}
    <div class="mt-6 flex items-center gap-4">
        <x-filament::select
            wire:model="selectedStaffId"
            placeholder="Choose Staff"
        >
            @foreach ($therapists as $person)
                <option value="{{ $person->id }}">
                    {{ $person->name }}
                </option>
            @endforeach
        </x-filament::select>

        <x-filament::button
            wire:click="assign"
            :disabled="!$selectedRoomId || !$selectedSlotId || !$selectedTherapistId"
        >
            Assign
        </x-filament::button>
    </div>

</x-filament::page>
