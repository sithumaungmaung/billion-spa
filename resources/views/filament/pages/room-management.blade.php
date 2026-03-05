<x-filament-panels::page>

    {{-- <x-filament::loading-indicator class="h-5 w-5" /> --}}
    <x-filament::section>
        {{-- <x-slot name="heading">
            Daily Room Service Schedule
        </x-slot> --}}

        <x-slot name="description">
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center gap-3">
                    {{-- <input type="date" wire:model.live="date" name="date"
                        class="rounded-lg border-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:text-white focus:ring-primary-500">

                    @if ($date === now()->toDateString())
                        <span class="text-green-600 font-medium px-2 py-1 bg-green-50 dark:bg-green-500/10 rounded-md">
                            Today
                        </span>
                    @endif --}}
                    <x-filament::button wire:click="checkBill" size="md" class="min-w-[100px]" :disabled="!$this->checkRoomIds">
                        Check Bill
                    </x-filament::button>

                    <x-filament::button wire:click="unassigRoom" size="md" color="danger" class="min-w-[100px]"
                        wire:confirm="Are you sure you want to unassign room and therapist?" :disabled="!$this->selectedRoom">
                        Unassign @if ($this->selectedRoom)
                            - {{ $this->selectedRoom->room->name }}
                        @endif
                    </x-filament::button>
                </div>

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
                    <div class="space-y-3 max-h-[50vh] overflow-y-auto">
                        @if ($this->rooms->isEmpty())
                            <div class="text-gray-600 dark:text-gray-400 text-center items-center">
                                No active rooms found.
                            </div>
                        @endif
                        @foreach ($this->rooms as $room)
                            @php
                                $isSelected =
                                    $this->selectedRoom &&
                                    $this->selectedRoom->id == $room->id &&
                                    $room->invoice_id == null;

                            @endphp


                            <div class="flex items-center gap-4"> {{-- Parent Container --}}

                                {{-- Room Card --}}
                                <div @if (!$room->invoice_id) wire:click="selectRoom({{ $room->id }})" @endif
                                    @class([
                                        'flex-1 flex items-center justify-between p-4 rounded-xl border transition-all duration-200 shadow-sm',
                                        'bg-white border-gray-200 hover:border-primary-500 dark:bg-white/5 dark:border-white/10 cursor-pointer' =>
                                            !$isSelected && !$room->invoice_id,
                                        'bg-primary-50 border-primary-500 ring-1 ring-primary-500 dark:bg-primary-500/10 cursor-pointer' =>
                                            $isSelected && !$room->invoice_id,
                                        'bg-gray-50 border-gray-200 opacity-60 pointer-events-none dark:bg-white/5 dark:border-white/5' =>
                                            $room->invoice_id,
                                    ])>

                                    <div class="flex-1">
                                        {{-- Top Row: Room Name --}}
                                        <div class="flex items-center gap-2 mb-2">
                                            <x-filament::icon icon="heroicon-m-home-modern"
                                                class="h-5 w-5 text-gray-400" />
                                            <h3
                                                class="font-bold text-lg {{ $isSelected ? 'text-primary-700 dark:text-primary-400' : 'text-gray-950 dark:text-white' }}">
                                                Room - {{ $room->room->name }}
                                            </h3>
                                        </div>

                                        {{-- Details Grid --}}
                                        <div class="flex flex-wrap items-center gap-x-6 gap-y-3 text-sm">
                                            @php
                                                $start = \Carbon\Carbon::parse($room->start_time);
                                                $end = \Carbon\Carbon::parse($room->end_time);
                                                $isNextDay = !$start->isSameDay($end);
                                                $diffInHours = ceil($start->diffInMinutes($end) / 60);
                                                $isChecked = in_array($room->id, $this->checkRoomIds);

                                                // Calculate the difference in calendar days
                                                // $dayDiff = $start->diffInDays($end->copy()->startOfDay());

                                                // Alternatively, if you want "24 hour periods", use:
                                                // $dayDiff = (int) $start->diffInDays($end);

                                                // just day by day
                                                $dayDiff = $start
                                                    ->copy()
                                                    ->startOfDay()
                                                    ->diffInDays($end->copy()->startOfDay());

                                            @endphp

                                            <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                                <x-filament::icon icon="heroicon-m-calendar"
                                                    class="h-4 w-4 text-primary-500" />
                                                <div class="flex flex-col">
                                                    <span class="font-bold text-gray-900 dark:text-white">
                                                        {{ $start->format('M d, Y') }}
                                                    </span>
                                                    <span class="text-xs">
                                                        {{ $start->format('g:i A') }} - {{ $end->format('g:i A') }}

                                                        @if ($dayDiff > 0)
                                                            <span
                                                                class="text-warning-600 dark:text-warning-400 font-medium">
                                                                ({{ $dayDiff }}
                                                                {{ str('Day')->plural($dayDiff) }})
                                                            </span>
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                                <x-filament::icon icon="heroicon-m-user" class="h-4 w-4" />
                                                <span>{{ $room->therapist->name }}</span>
                                            </div>

                                            <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                                <x-filament::icon icon="heroicon-m-tag" class="h-4 w-4" />
                                                <span
                                                    class="px-2 py-0.5 bg-gray-100 dark:bg-gray-800 rounded-full text-xs font-medium">
                                                    {{ $room->therapistType->title }}
                                                </span>
                                            </div>

                                            <div
                                                class="flex items-center gap-2 text-gray-500 bg-gray-50 dark:bg-white/5 px-2 py-1 rounded">
                                                <x-filament::icon icon="heroicon-m-bolt" class="h-3.5 w-3.5" />
                                                <span class="text-xs uppercase tracking-wider font-semibold">
                                                    {{ $diffInHours }} {{ str('Section')->plural($diffInHours) }}
                                                </span>
                                            </div>

                                            {{-- <div class="flex items-center">
                                                @if ($room->invoice_id)
                                                    <span
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-100 text-success-700 dark:bg-success-500/10 dark:text-success-400 border border-success-200 dark:border-success-500/20">
                                                        <x-filament::icon icon="heroicon-m-check-badge"
                                                            class="h-3.5 w-3.5" />
                                                        Paid
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-danger-100 text-danger-700 dark:bg-danger-500/10 dark:text-danger-400 border border-danger-200 dark:border-danger-500/20">
                                                        <x-filament::icon icon="heroicon-m-credit-card"
                                                            class="h-3.5 w-3.5" />
                                                        Unpaid
                                                    </span>
                                                @endif
                                            </div> --}}
                                        </div>
                                    </div>
                                </div>

                                {{-- Status Indicator (Now Outside to the Right) --}}
                                <div class="flex-shrink-0 mr-3">
                                    <button type="button"
                                        @if (!$room->invoice_id) wire:click="checkRoom({{ $room->id }})" @endif
                                        @disabled($room->invoice_id) @class([
                                            'flex items-center justify-center w-6 h-6 rounded-full transition-all duration-200',
                                            'bg-primary-500 text-white shadow-lg shadow-primary-500/30 scale-110' =>
                                                $isChecked && !$room->invoice_id,
                                            'bg-gray-100 text-gray-400 dark:bg-white/5 hover:bg-gray-200' =>
                                                !$isChecked && !$room->invoice_id,
                                            'bg-gray-200 text-gray-300 cursor-not-allowed opacity-50' =>
                                                $room->invoice_id,
                                        ])>
                                        <x-filament::icon
                                            icon="{{ $room->invoice_id ? 'heroicon-m-lock-closed' : 'heroicon-m-check' }}"
                                            class="{{ $isChecked ? 'h-4 w-4' : 'h-3 w-3 opacity-100' }}" />
                                    </button>
                                </div>

                            </div>
                        @endforeach
                    </div>

                </div>
            </div>

            {{-- Right Side --}}
            <div class="flex-1 filament-form-container">
                <div class="">
                    <div class="border border-gray-300 dark:border-gray-700 rounded-2xl  top-4 p-4 space-y-4 ">
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
                                        <x-filament::input wire:model="selectedProductQty" placeholder="Qty"
                                            type="number" min="1" />
                                    </x-filament::input.wrapper>
                                </div>
                            </div>
                            <x-filament::button wire:click="addProduct" size="md" class="min-w-[100px]"
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
                                            <option value="{{ $service->id }}">
                                                {{ $service->title }}
                                                - {{ number_format($service->price) }}
                                                {{ $this->selectedRoom && in_array($service->id, $this->selectedRoom->extraServices->pluck('id')->toArray()) ? '(included)' : '' }}
                                            </option>
                                        @endforeach
                                    </x-filament::input.select>
                                </x-filament::input.wrapper>
                            </div>
                            <div class="w-20">
                                <x-filament::input.wrapper>
                                    <x-filament::input wire:model="selectedExtraServiceQty" placeholder="Qty"
                                        type="number" min="1" />
                                </x-filament::input.wrapper>
                            </div>
                            <x-filament::button wire:click="updateExtraService" size="md" multiple
                                class="min-w-[100px]" :disabled="!$selectedRoom || !$this->selectedExtraServiceId">
                                Add
                            </x-filament::button>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="flex-1 grid grid-cols-2 gap-3">
                                <div>
                                    <x-filament::input.wrapper required>
                                        <x-filament::input type="datetime-local" wire:model="selectedStartTime"
                                            value="{{ $this->selectedRoom ? Carbon\Carbon::parse($this->selectedRoom->start_time)->format('Y-m-d\TH:i') : '' }}" />
                                    </x-filament::input.wrapper>
                                </div>
                                <div>
                                    <x-filament::input.wrapper required>
                                        <x-filament::input type="datetime-local" wire:model="selectedEndTime"
                                            value="{{ $this->selectedRoom ? Carbon\Carbon::parse($this->selectedRoom->end_time)->format('Y-m-d\TH:i') : '' }}" />
                                    </x-filament::input.wrapper>
                                </div>
                            </div>
                            <x-filament::button wire:click="updateTime" size="md" class="min-w-[100px]"
                                :disabled="!$selectedRoom">
                                Update
                            </x-filament::button>
                        </div>

                    </div>



                    {{-- // Sale Products // --}}
                    <div class="mt-4">
                        @if (count($this->extraServiceAndProductSales['saleProducts'] ?? []) > 0 ||
                                count($this->extraServiceAndProductSales['saleExtraServices'] ?? []) > 0)
                            <x-filament::section heading="Ordered Products & Services" collapsible>
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
                                            {{-- @dd($this->extraServiceAndProductSales) --}}
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
                                                            wire:click="removeProduct({{ $product->id }})"
                                                            color="danger" size="xs">
                                                            Remove
                                                        </x-filament::button>

                                                        <x-filament::button class="mr-1 text-white"
                                                            wire:click="reduceProduct({{ $product->id }})"
                                                            :disabled="$product->quantity <= 1" color="primary" size="xs">
                                                            -
                                                        </x-filament::button>

                                                        <x-filament::button class="mr-1 text-white bg-green-900"
                                                            wire:click="addMoreProduct({{ $product->id }})"
                                                            size="xs">
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
                                                        {{ $service->quantity }}
                                                    </td>

                                                    <td class="px-4 py-3 text-right font-mono">
                                                        {{ number_format($service->unit_price) }}
                                                    </td>

                                                    <td class="px-4 py-3 text-right font-mono font-semibold ">

                                                        {{ number_format($service->total_price) }}
                                                    </td>

                                                    <td class="px-4 py-3 text-right font-mono w-[180px]">
                                                        <x-filament::button class="mr-1"
                                                            wire:click="removeService({{ $service->id }})"
                                                            color="danger" size="xs">
                                                            Remove
                                                        </x-filament::button>

                                                        <x-filament::button class="mr-1 text-white"
                                                            wire:click="reduceExtraService({{ $service->id }})"
                                                            color="primary" size="xs">
                                                            -
                                                        </x-filament::button>

                                                        <x-filament::button class="mr-1 text-white bg-green-900"
                                                            wire:click="addMoreExtraService({{ $service->id }})"
                                                            size="xs">
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
                        @endif
                    </div>
                </div>

            </div>

        </div>



    </x-filament::section>





    <!-- <div wire:loading.flex class="fixed inset-0 bg-black/30 z-50 items-center justify-center">
        <x-filament::loading-indicator class="h-10 w-10 text-white" />
    </div> -->
</x-filament-panels::page>


<script>
    window.addEventListener('check.bill', event => {
        window.open(event.detail.url, '_blank');
    });
</script>
