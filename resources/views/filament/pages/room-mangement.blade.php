<x-filament-panels::page>

    {{-- <x-filament::loading-indicator class="h-5 w-5" /> --}}
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


        {{-- <x-filament::input.wrapper label="Select Room">
            <x-filament::input.select wire:model.live="searchRoomId">
                <option value="0">Find Room</option>
                @foreach ($rooms as $room)
                    <option value="{{ $room->id }}">{{ $room->name }}</option>
                @endforeach
            </x-filament::input.select>

        </x-filament::input.wrapper> <br> --}}

        <div class="py-4 gap-6 sticky left-0 z-25">
            <div class="flex items-end gap-4">
                <div class="flex-1 filament-form-container sticky left-0 z-25">
                    {{ $this->room_form }}
                </div>

                <x-filament::button wire:click="pickRoom" size="md" class="w-24">
                    Select
                </x-filament::button>

            </div>
        </div>



        <div class="flex flex-wrap items-end gap-6">

            <div class="flex-1 min-w-[300px]">
                <x-filament::input.wrapper label="Select Type">
                    <x-filament::input.select wire:model.live="selectedType">
                        @foreach ($therapistTypes as $type)
                            <option value="{{ $type->id }}">
                                {{ $type->title }} -
                                ({{ $type->price == 0 ? 'No added fees' : number_format($type->price) }})
                            </option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>

            </div>

            <div class="flex-1 min-w-[300px]">
                <div class="flex items-end gap-4">
                    <div class="flex-1 filament-form-container">
                        {{ $this->therapist_form }}
                    </div>

                    <x-filament::button wire:click="assign" class="w-24" size="md" :disabled="!$selectedRoomId || !$selectedSlotId || !$selectedType">
                        Assign
                    </x-filament::button>
                </div>
            </div>

        </div>


        <div class="flex flex-wrap items-end gap-6 mt-5">

            <div class="flex-1 min-w-[300px]">
                <div class="flex items-end gap-4">
                    <div class="flex-1">
                        {{-- <x-filament::input.wrapper label="Select Product">
                            <x-filament::input.select wire:model.live="selectedProductId">
                                <option value="" class="text-gray-400" selected>Choose Product</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">
                                        {{ $product->name }} ({{ number_format($product->price) }})
                                    </option>
                                @endforeach
                            </x-filament::input.select>
                        </x-filament::input.wrapper> --}}
                        <div class="flex-1 filament-form-container sticky left-0 z-22">
                            {{ $this->product_form }}
                        </div>
                    </div>

                    <div class="w-[60px]">
                        <x-filament::input.wrapper>
                            <x-filament::input wire:model="selectedProductQty" placeholder="Qty" type="number"
                                min="1" :disabled="!$selectedRoomId || !$selectedSlotId" />
                        </x-filament::input.wrapper>
                    </div>

                    <x-filament::button wire:click="addProduct" class="w-24" size="md" {{-- :disabled="$this->canAddProduct" --}}
                        :disabled="!$selectedRoomId || !$selectedSlotId">
                        Add
                    </x-filament::button>
                </div>
            </div>

            <div class="flex-1 min-w-[300px]">
                <div class="flex items-end gap-2">
                    <div class="flex-1">
                        <x-filament::input.wrapper label="Select Extra Service">
                            <x-filament::input.select wire:model.live="selectedExtraServiceId">
                                <option value="" class="text-gray-400" selected>Choose Extra Service</option>
                                @foreach ($extraServices as $service)
                                    <option value="{{ $service->id }}">
                                        {{ $service->title }} ({{ number_format($service->price ?? 0) }})
                                    </option>
                                @endforeach
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </div>

                    <x-filament::button wire:click="addExtraService" class="w-24 ml-2" size="md" :disabled="!$selectedExtraServiceId || !$selectedRoomId || !$selectedSlotId">
                        Add
                    </x-filament::button>
                </div>
            </div>

        </div>


        <div class="mt-5 overflow-x-auto border border-gray-200 dark:border-white/10 rounded-lg relative">

            <table class="w-full text-sm text-left table-fixed">
                <thead class="bg-gray-50 dark:bg-white/5 uppercase text-xs">
                    <tr>
                        <th
                            class="p-3 border-b dark:border-white/10 w-[100px] sticky left-0 z-20  bg-gray-50 dark:bg-gray-700">
                            Room</th>
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
                            <td
                                class="sticky left-0 z-20 p-3 font-bold border-b border-l
                                bg-gray-50 dark:bg-gray-900
                                border-gray-400 dark:border-white/10
                                shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
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

                                    @if ($this->checkBill($room->id, $slot->id))
                                        <span class="text-green-600 font-medium">(Paid)</span>
                                    @elseif($this->getThapistName($room->id, $slot->id))
                                        <input type="checkbox" wire:model.live="selectedRoomIdsForBill"
                                            style="width:20px; height:20px"
                                            value="{{ $this->getDailyRoomRecordId($room->id, $slot->id) }}">
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div> <br>
        <x-filament::button wire:click="goToCheckBill" color="primary" class="mt-1" :disabled="!$selectedRoomIdsForBill">
            Check Bill
        </x-filament::button>
    </x-filament::section>


    {{-- Mini Info --}}
    @if ($isExistingRecord)

        <x-filament::section heading="Room Info" collapsible collapsed>
            @if ($selectedRoomId || $selectedSlotId)
                <div class=" items-center justify-start ">
                    <div class="my-3 font-medium ">Room :
                        <span class="text-primary-600">
                            {{ $selectedRoomName ?? '—' }}
                        </span>

                    </div>

                    <div class="my-3 font-medium">
                        Therapist :
                        @if ($selectedTherapistName)
                            <span class="text-primary-600">{{ $selectedTherapistName }}</span>
                        @else
                            <span class="text-gray-400 italic">Not Assigned</span>
                        @endif
                    </div>
                    <div class="my-3 font-medium">
                        Therapist Type :

                        <span class="text-primary-600">{{ $selectedTherapistType }}</span>

                    </div>

                    {{-- @if (count($selectedExtraServicesList) > 0)
                        <div class="my-3 font-medium">
                            Extra Services :
                            @foreach ($selectedExtraServicesList as $extraService)
                                <span class="text-primary-600">{{ $extraService['title'] }}</span>
                                @if (count($selectedExtraServicesList) > 1 && collect($selectedExtraServicesList)->last() !== $extraService)
                                    ,
                                @endif
                            @endforeach
                        </div>
                    @endif --}}


                    {{-- <div class="my-3 font-medium">Time : {{ $selectedTimeSection ?? '—' }}</div> --}}
                    <div class="my-3 font-medium">Start : {{ date('h:i A', strtotime($selectedStartTime)) ?? '—' }}
                    </div>
                    <div class="my-3 font-medium">End : {{ date('h:i A', strtotime($selectedEndTime)) ?? '—' }}</div>

                    <div class="my-3 mt-2">
                        <br>
                        <x-filament::button wire:click="removeTherapist" color="danger" size="sm"
                            wire:confirm="Are you sure you want to unassign room and therapist?">
                            Unassign room and therapist
                        </x-filament::button>
                    </div>
                </div>
            @endif
        </x-filament::section>



        {{-- Ordered Items --}}
        <x-filament::section heading="Ordered Items" collapsible collapsed>
            <x-filament::card>

                <table
                    class="w-full text-sm text-left border-collapse
           border border-gray-200 dark:border-white/10">
                    <thead class="bg-gray-100 text-gray-700
                   dark:bg-white/5 dark:text-gray-300">
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <th class="px-4 py-3 w-12 text-center">#</th>
                            <th class="px-4 py-3">Title</th>
                            <th class="px-4 py-3 text-right">Unit</th>
                            <th class="px-4 py-3 text-right">Price</th>
                            <th class="px-4 py-3 text-right">Total</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100
                   dark:divide-white/10">

                        @if (array_key_exists('productSales', $this->selectedRoomRecordProducts))
                            @foreach ($this->selectedRoomRecordProducts['productSales'] as $key => $product)
                                <tr
                                    class="hover:bg-gray-50 divide-x divide-gray-100 dark:divide-white/10 divide-y
                               dark:hover:bg-white/5 transition">
                                    <td class="px-4 py-3 text-center text-gray-500">
                                        {{ $key + 1 }}
                                    </td>

                                    <td class="px-4 py-3 font-medium">
                                        {{ $product->product->name }}
                                    </td>

                                    <td class="px-4 py-3 text-right">
                                        {{ $product->quantity }}
                                    </td>

                                    <td class="px-4 py-3 text-right font-mono">
                                        {{ number_format($product->unit_price) }}
                                    </td>

                                    <td class="px-4 py-3 text-right font-mono font-semibold ">
                                        {{-- {{ number_format($product->quantity * $product->unit_price) }} --}}
                                        {{ number_format($product->total_price) }}
                                    </td>

                                    <td class="px-4 py-3 text-right font-mono w-[270px]">
                                        <x-filament::button class="mr-1"
                                            wire:click="removeProduct({{ $product->id }})" color="danger"
                                            size="sm">
                                            Remove
                                        </x-filament::button>

                                        <x-filament::button class="mr-1 text-white"
                                            wire:click="reduceProduct({{ $product->id }})" :disabled="$product->quantity <= 1"
                                            color="primary" size="sm">
                                            Reduce
                                        </x-filament::button>

                                        <x-filament::button class="mr-1 text-white bg-green-900"
                                            wire:click="addMoreProduct({{ $product->id }})" size="sm">
                                            Add
                                        </x-filament::button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        <td class="px-4 py-3 text-right font-mono font-semibold " colspan="4">
                            Product Total Bill
                        </td>
                        <td class="px-4 py-3 text-right font-mono font-semibold ">
                            {{ number_format($this->selectedRoomRecordProducts['productSaleTotal'] ?? 0) }}
                        </td>
                    </tbody>
                </table>
            </x-filament::card>
        </x-filament::section>


        {{-- Extra Services --}}
        {{-- @dd($selectedExtraServicesList); --}}
        <x-filament::section heading="Extra Services" collapsible collapsed>
            <x-filament::card>

                <table
                    class="w-full text-sm text-left border-collapse
           border border-gray-200 dark:border-white/10">
                    <thead class="bg-gray-100 text-gray-700
                   dark:bg-white/5 dark:text-gray-300">
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <th class="px-4 py-3 w-12 text-center">#</th>
                            <th class="px-4 py-3">Title</th>
                            <th class="px-4 py-3 text-right">Price</th>
                            <th class="px-4 py-3 text-right">Total</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100
                   dark:divide-white/10">


                        @foreach ($this->selectedExtraServicesList['extraServices'] as $key => $service)
                            <tr
                                class="hover:bg-gray-50 divide-x divide-gray-100 dark:divide-white/10 divide-y
                               dark:hover:bg-white/5 transition">
                                <td class="px-4 py-3 text-center text-gray-500">
                                    {{ $key + 1 }}
                                </td>

                                <td class="px-4 py-3 font-medium">
                                    {{ $service['extra_service']['title'] }}
                                </td>

                                <td class="px-4 py-3 text-right font-mono">
                                    {{ number_format($service['unit_price']) }}

                                </td>

                                <td class="px-4 py-3 text-right font-mono font-semibold ">

                                    {{ number_format($service['total_price']) }}
                                </td>

                                <td class="px-4 py-3 text-right font-mono w-[270px]">
                                    <x-filament::button class="mr-1"
                                        wire:click="removeExtraService({{ $service['id'] }})" color="danger"
                                        size="sm">
                                        Remove
                                    </x-filament::button>


                                    {{-- <x-filament::button class="mr-1 text-white bg-green-900"
                                        wire:click="addMoreProduct({{ $service['id'] }})" size="sm">
                                        Add
                                    </x-filament::button> --}}
                                </td>
                            </tr>
                        @endforeach

                        <td class="px-4 py-3 text-right font-mono font-semibold " colspan="4">
                            Extra Service Total Bill
                        </td>
                        <td class="px-4 py-3 text-right font-mono font-semibold ">
                            {{ number_format($selectedExtraServicesList['total']) }}
                        </td>
                    </tbody>
                </table>
            </x-filament::card>
        </x-filament::section>



    @endif

    <!-- <div wire:loading.flex class="fixed inset-0 bg-black/30 z-50 items-center justify-center">
        <x-filament::loading-indicator class="h-10 w-10 text-white" />
    </div> -->
</x-filament-panels::page>
