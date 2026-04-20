<x-filament-panels::page>

    <x-filament::section compact class="p-0 gap-4">

        <x-filament::section.heading class="mb-2">
            Prepid Deduction
        </x-filament::section.heading>

        <div class="flex items-end gap-4">

            {{-- Customer --}}
            <div class="flex-1">
                <div class="mb-2">Customer</div>
                <x-filament::input.wrapper>
                    {{ $this->customer_form }}
                </x-filament::input.wrapper>
            </div>

            {{-- Amount --}}
            <div class="flex-1">
                <div class="grid grid-cols-2 gap-x-4">

                    <x-filament-forms::field-wrapper label="Current Amount">
                        <x-filament::input.wrapper class="bg-gray-50 dark:bg-white/5">
                            <x-filament::input type="number" wire:model="currentAmount" readonly />
                        </x-filament::input.wrapper>
                    </x-filament-forms::field-wrapper>

                    <x-filament-forms::field-wrapper label="Add Amount">
                        <x-filament::input.wrapper>
                            <x-filament::input type="number" wire:model.live="prepaidAmount" />
                        </x-filament::input.wrapper>
                    </x-filament-forms::field-wrapper>

                </div>
            </div>

            {{-- BUTTON HERE --}}
            <div>
                <x-filament::button color="primary" size="lg" wire:click="AddPrepaidAmount" :disabled="!$selectedCustomer || !$prepaidAmount">
                    Add Prepaid
                </x-filament::button>
            </div>

        </div>

    </x-filament::section>

</x-filament-panels::page>
