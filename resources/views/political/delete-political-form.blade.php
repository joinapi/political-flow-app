@php
    $modals = \Joinapi\PoliticalFlow\PoliticalFlow::getModals();
@endphp

<x-political-flow::grid-section md="2">
    <x-slot name="title">
        {{ __('political-flow::default.action_section_titles.delete_portal') }}
    </x-slot>

    <x-slot name="description">
        {{ __('political-flow::default.action_section_descriptions.delete_political') }}
    </x-slot>

    <x-filament::section>
        <div class="grid gap-y-6">
            <div class="max-w-xl text-sm text-gray-600 dark:text-gray-400">
                {{ __('political-flow::default.subheadings.political.delete_political') }}
            </div>

            <!-- Delete Company Confirmation Modal -->
            <x-filament::modal id="confirmingPoliticalDeletion" icon="heroicon-o-exclamation-triangle" icon-color="danger" alignment="{{ $modals['alignment'] }}" footer-actions-alignment="{{ $modals['formActionsAlignment'] }}" width="{{ $modals['width'] }}">
                <x-slot name="trigger">
                    <div class="text-left">
                        <x-filament::button color="danger">
                            {{ __('political-flow::default.buttons.delete_political') }}
                        </x-filament::button>
                    </div>
                </x-slot>

                <x-slot name="heading">
                    {{ __('political-flow::default.modal_titles.delete_political') }}
                </x-slot>

                <x-slot name="description">
                    {{ __('political-flow::default.modal_descriptions.delete_political') }}
                </x-slot>

                <x-slot name="footerActions">
                    @if($modals['cancelButtonAction'])
                        <x-filament::button color="gray" wire:click="cancelPoliticalDeletion">
                            {{ __('political-flow::default.buttons.cancel') }}
                        </x-filament::button>
                    @endif

                    <x-filament::button color="danger" wire:click="deletePolitical">
                        {{ __('political-flow::default.buttons.delete_political') }}
                    </x-filament::button>
                </x-slot>
            </x-filament::modal>
        </div>
    </x-filament::section>
</x-political-flow::grid-section>
