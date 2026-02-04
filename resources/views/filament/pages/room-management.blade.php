<x-filament-panels::page>

    {{-- <x-filament::loading-indicator class="h-5 w-5" /> --}}
    <x-filament::section>
        <x-slot name="heading">
            Daily Room Service Schedule
        </x-slot>

        <x-slot name="description">
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center gap-3">
                    <input type="date" wire:model.live="date" name="date"
                        class="rounded-lg border-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:text-white focus:ring-primary-500">

                    @if ($date === now()->toDateString())
                        <span class="text-green-600 font-medium px-2 py-1 bg-green-50 dark:bg-green-500/10 rounded-md">
                            Today
                        </span>
                    @endif
                </div>

                <x-filament::button wire:click="checkBill" size="md" class="min-w-[100px]">
                    Check Bill
                </x-filament::button>
            </div>
        </x-slot>



        <div class="flex py-4 gap-6 sticky left-0 top-0 z-25">
            <div class="w-[480px] shrink-0">
                <div class="flex items-end gap-4">
                    <div class="flex-1 filament-form-container sticky left-0 z-25">
                        {{ $this->room_form }}
                    </div>

                    {{-- <x-filament::button wire:click="selectRoom" size="md" class="w-24">
                        Select
                    </x-filament::button> --}}

                </div>

                <div class="mt-4">
                    <div class="space-y-3">
                        @foreach ($this->rooms as $room)
                            @php
                                $isSelected = $this->selectedRoom && $this->selectedRoom->id == $room->id;

                            @endphp

                            <div wire:click="selectRoom({{ $room->id }})" @class([
                                'flex items-center justify-between p-4 rounded-xl border cursor-pointer transition-all duration-200 shadow-sm',
                                'bg-white border-gray-200 hover:border-primary-500 dark:bg-white/5 dark:border-white/10' => !$isSelected,
                                'bg-primary-50 border-primary-500 ring-1 ring-primary-500 dark:bg-primary-500/10' => $isSelected,
                            ])>
                                <div class="flex-1">
                                    {{-- Top Row: Room Name --}}
                                    <div class="flex items-center gap-2 mb-2">
                                        <x-filament::icon icon="heroicon-m-home-modern" class="h-5 w-5 text-gray-400" />
                                        <h3
                                            class="font-bold text-lg @if ($isSelected) text-primary-700 dark:text-primary-400 @else text-gray-950 dark:text-white @endif">
                                            Room - {{ $room->room->name }}
                                        </h3>
                                    </div>

                                    {{-- Details Grid --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                                        <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                            <x-filament::icon icon="heroicon-m-user" class="h-4 w-4" />
                                            <span>
                                                {{ $room->therapist->name }}</span>
                                        </div>

                                        <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                            <x-filament::icon icon="heroicon-m-tag" class="h-4 w-4" />
                                            <span>
                                                {{ $room->therapistType->title }}</span>
                                        </div>

                                        <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                            <x-filament::icon icon="heroicon-m-clock" class="h-4 w-4" />
                                            <span>{{ $room->start_time }} - {{ $room->end_time }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Status Indicator --}}
                                <div class="ml-4">
                                    @php
                                        $isSelected = in_array($room->id, $this->checkRoomIds);
                                    @endphp

                                    <button type="button" wire:click="checkRoom({{ $room->id }})"
                                        @class([
                                            'flex items-center justify-center w-8 h-8 rounded-full transition-all duration-200',
                                            'bg-primary-500 text-white shadow-lg shadow-primary-500/30 scale-110' => $isSelected,
                                            'bg-gray-100 text-gray-400 dark:bg-white/5 hover:bg-gray-200' => !$isSelected,
                                        ])>
                                        <x-filament::icon icon="heroicon-m-check"
                                            class="{{ $isSelected ? 'h-3 w-3' : 'h-2 w-2 opacity-50' }}" />
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>

            <div class="flex-1">
                <div class="border border-gray-300 dark:border-gray-700 rounded-2xl p-4 space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="flex-1 grid grid-cols-2 gap-3">
                            <div>
                                {{ $this->therapist_form }}
                            </div>
                            <div>
                                <x-filament::input.wrapper>
                                    <x-filament::input.select wire:model.live="selectedTherapistTypeId" required>
                                        <option value="0">Choose Specialization</option>
                                        @foreach ($therapistTypes as $type)
                                            <option value="{{ $type->id }}" @disabled($this->selectedRoom && $this->selectedRoom->therapistType->id == $type->id)>
                                                {{ $type->title }}
                                                {{ $this->selectedRoom && $this->selectedRoom->therapistType->id == $type->id ? '(Selected)' : '' }}
                                            </option>
                                        @endforeach
                                    </x-filament::input.select>
                                </x-filament::input.wrapper>
                            </div>
                        </div>
                        <x-filament::button wire:click="switchTherapistAndTherapistType" size="md"
                            class="min-w-[100px]" :disabled="!$selectedRoom">
                            Switch
                        </x-filament::button>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-800" />

                    <div class="flex items-start gap-3">
                        <div class="flex-1 flex gap-3">
                            <div class="flex-1">
                                {{ $this->product_form }}
                            </div>
                            <div class="w-20">
                                <x-filament::input.wrapper>
                                    <x-filament::input wire:model="selectedProductQty" placeholder="Qty" type="number"
                                        min="1" />
                                </x-filament::input.wrapper>
                            </div>
                        </div>
                        <x-filament::button wire:click="addProduct" size="md" color="info" class="min-w-[100px]"
                            :disabled="!$selectedRoom">
                            Add
                        </x-filament::button>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-800" />

                    <div class="flex items-start gap-3">
                        <div class="flex-1">

                            <x-filament::input.wrapper>
                                <x-filament::input.select wire:model.live="selectedExtraServiceId" required>
                                    <option value="0">Choose Extra Service</option>
                                    @foreach ($extraServices as $service)
                                        <option value="{{ $service->id }}" @disabled($this->selectedRoom && in_array($service->id, $this->selectedRoom->extraServices->pluck('id')->toArray()))>
                                            {{ $service->title }}
                                            {{ $this->selectedRoom && in_array($service->id, $this->selectedRoom->extraServices->pluck('id')->toArray()) ? '(included)' : '' }}
                                        </option>
                                    @endforeach
                                </x-filament::input.select>
                            </x-filament::input.wrapper>
                        </div>
                        <x-filament::button wire:click="updateExtraService" size="md" color="gray" multiple
                            class="min-w-[100px]" :disabled="!$selectedRoom">
                            Update
                        </x-filament::button>
                    </div>
                </div>

                {{-- // Sale Products // --}}
                <div class="mt-4">

                    <x-filament::section heading="Ordered Products & Services" collapsible collapsed>
                        {{-- <x-filament::card> --}}

                        <table
                            class="w-full text-sm text-left border-collapse border border-gray-200 dark:border-white/10">
                            <thead class="bg-gray-100 text-gray-700 dark:bg-white/5 dark:text-gray-300">
                                <tr class="border-b border-gray-200 dark:border-white/10">
                                    <th class="px-4 py-3 w-12 text-center">#</th>
                                    <th class="px-4 py-3">Title</th>
                                    <th class="px-4 py-3 text-right">Unit</th>
                                    <th class="px-4 py-3 text-right">Price</th>
                                    <th class="px-4 py-3 text-right">Total</th>
                                    <th class="px-4 py-3 text-right">Action</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100 dark:divide-white/10">

                                @if ($this->selectedRoom)

                                    @foreach ($this->extraServiceAndProductSales['saleProducts'] as $key => $product)
                                        <tr
                                            class="hover:bg-gray-50 divide-x divide-gray-100 dark:divide-white/10 divide-y dark:hover:bg-white/5 transition">
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

                                                {{ number_format($product->total_price) }}
                                            </td>

                                            <td class="px-4 py-3 text-right font-mono w-[180px]">
                                                <x-filament::button class="mr-1"
                                                    wire:click="removeProduct({{ $product->id }})" color="danger"
                                                    size="xs">
                                                    Remove
                                                </x-filament::button>

                                                <x-filament::button class="mr-1 text-white"
                                                    wire:click="reduceProduct({{ $product->id }})" :disabled="$product->quantity <= 1"
                                                    color="primary" size="xs">
                                                    -
                                                </x-filament::button>

                                                <x-filament::button class="mr-1 text-white bg-green-900"
                                                    wire:click="addMoreProduct({{ $product->id }})" size="xs">
                                                    +
                                                </x-filament::button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    {{--  --}}

                                    @foreach ($this->extraServiceAndProductSales['saleExtraServices'] as $key => $service)
                                        <tr
                                            class="hover:bg-gray-50 divide-x divide-gray-100 dark:divide-white/10 divide-y dark:hover:bg-white/5 transition">
                                            <td class="px-4 py-3 text-center text-gray-500">
                                                {{ $key + 1 }}
                                            </td>

                                            <td class="px-4 py-3 font-medium">
                                                {{ $service->extraService->title }}
                                            </td>

                                            <td class="px-4 py-3 text-right">
                                                1
                                            </td>

                                            <td class="px-4 py-3 text-right font-mono">
                                                {{ number_format($service->unit_price) }}
                                            </td>

                                            <td class="px-4 py-3 text-right font-mono font-semibold ">

                                                {{ number_format($service->total_price) }}
                                            </td>

                                            <td class="px-4 py-3 text-right font-mono w-[180px]">
                                                <x-filament::button class="mr-1"
                                                    wire:click="removeProduct({{ $service->id }})" color="danger"
                                                    size="xs">
                                                    Remove
                                                </x-filament::button>

                                                <x-filament::button class="mr-1 text-white"
                                                    wire:click="reduceProduct({{ $service->id }})" color="primary"
                                                    size="xs">
                                                    -
                                                </x-filament::button>

                                                <x-filament::button class="mr-1 text-white bg-green-900"
                                                    wire:click="addMoreProduct({{ $product->id }})" size="xs">
                                                    +
                                                </x-filament::button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <td class="px-4 py-3 text-right font-mono font-semibold " colspan="4">
                                        Total Bill
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono font-semibold ">
                                        {{ number_format($this->extraServiceAndProductSales['saleTotal']) }}
                                    </td>
                                @endif




                                </td>
                            </tbody>
                        </table>
                        {{-- </x-filament::card> --}}
                    </x-filament::section>
                </div>

            </div>

        </div>



    </x-filament::section>





    <!-- <div wire:loading.flex class="fixed inset-0 bg-black/30 z-50 items-center justify-center">
        <x-filament::loading-indicator class="h-10 w-10 text-white" />
    </div> -->
</x-filament-panels::page>
