<x-filament-panels::page>
    <x-filament::section>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Date Input --}}
            <x-filament-forms::field-wrapper label="Selected Date" id="date">
                <x-filament::input.wrapper>
                    <x-filament::input id="date" type="date" wire:model.live="date" readonly />
                </x-filament::input.wrapper>
            </x-filament-forms::field-wrapper>

            {{-- Room Select --}}
            <x-filament-forms::field-wrapper label="Room" id="room">
                <x-filament::input.wrapper>
                    <x-filament::input.select id="room" wire:model.live="selectedRoomId">
                        <option value="0">Select a Room</option>
                        @foreach ($avaliableRooms as $room)
                            <option value="{{ $room->id }}">{{ $room->name }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </x-filament-forms::field-wrapper>

            {{-- Therapist Select --}}
            <x-filament-forms::field-wrapper label="Therapist" id="therapist">
                <x-filament::input.wrapper>
                    <x-filament::input.select id="therapist" wire:model.live="selectedTherapistId">
                        <option value="0">Choose Therapist</option>
                        @foreach ($avaliableTherapists as $therapist)
                            <option value="{{ $therapist->id }}">{{ $therapist->name }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </x-filament-forms::field-wrapper>

            {{-- Therapist Type Select --}}
            <x-filament-forms::field-wrapper label="Specialization / Type" id="type">
                <x-filament::input.wrapper>
                    <x-filament::input.select id="type" wire:model.live="selectedTherapistTypeId" required>
                        <option value="0">Choice Specialization</option>
                        @foreach ($therapistTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->title }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </x-filament-forms::field-wrapper>

            {{-- Start Time --}}
            <x-filament-forms::field-wrapper label="Start Time" id="startTime">
                <x-filament::input.wrapper>
                    <x-filament::input id="startTime" type="time" wire:model.live="startTime" />
                    <x-slot name="suffix">
                        <x-filament::button size="xs" wire:click="$set('startTime', '{{ now()->format('H:i') }}')">
                            Now
                        </x-filament::button>
                    </x-slot>
                </x-filament::input.wrapper>
            </x-filament-forms::field-wrapper>

            {{-- End Time --}}
            <x-filament-forms::field-wrapper label="End Time" id="endTime">
                <x-filament::input.wrapper>
                    <x-filament::input id="endTime" type="time" wire:model.live="endTime" />
                    <x-slot name="suffix">
                        <x-filament::button size="xs"
                            wire:click="$set('endTime', '{{ now()->addHour()->format('H:i') }}')">
                            Add one Hour
                        </x-filament::button>
                    </x-slot>
                </x-filament::input.wrapper>
            </x-filament-forms::field-wrapper>

        </div>

        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-white/10 flex justify-end gap-3">

            <x-filament::button wire:click="createRoomAssign" icon="heroicon-m-check" size="lg" :disabled="!$selectedRoomId ||
                !$selectedTherapistId ||
                !$selectedTherapistTypeId ||
                !$startTime ||
                !$endTime">
                Add Room Assign
            </x-filament::button>
        </div>

    </x-filament::section>
</x-filament-panels::page>
