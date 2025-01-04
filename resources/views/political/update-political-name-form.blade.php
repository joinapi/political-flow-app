<x-political-flow::grid-section md="2">
    <x-slot name="title">
        {{ __('political-flow::default.grid_section_titles.political_name') }}
    </x-slot>

    <x-slot name="description">
        {{ __('political-flow::default.grid_section_descriptions.political_name') }}
    </x-slot>

    <x-filament::section>
        <x-filament-panels::form wire:submit="updatePoliticalName">
                <!-- Political Owner Information -->
                <x-filament-forms::field-wrapper.label>
                    {{ __('political-flow::default.labels.political_owner') }}
                </x-filament-forms::field-wrapper.label>

                <div class="flex items-center text-sm">
                    <div class="flex-shrink-0">
                        <x-filament-panels::avatar.user :user="$political->owner" style="height: 3rem; width: 3rem;" />
                    </div>
                    <div class="ml-4">
                        <div class="font-medium text-gray-900 dark:text-gray-200">{{ $political->owner->name }}</div>
                        <div class="text-gray-600 dark:text-gray-400">{{ $political->owner->email }}</div>
                    </div>
                </div>

                <!-- political Name -->
                <x-filament-forms::field-wrapper id="name" statePath="name" required="required" label="{{ __('political-flow::default.labels.political_name') }}">
                    <x-filament::input.wrapper class="overflow-hidden">
                        <x-filament::input id="name" type="text" maxlength="255" wire:model="state.name" :disabled="!Gate::check('update', $political)" />
                    </x-filament::input.wrapper>
                </x-filament-forms::field-wrapper>

                @if (Gate::check('update', $political))
                    <div class="text-left">
                        <x-filament::button type="submit">
                            {{ __('political-flow::default.buttons.save') }}
                        </x-filament::button>
                    </div>
                @endif
        </x-filament-panels::form>
    </x-filament::section>
</x-political-flow::grid-section>
