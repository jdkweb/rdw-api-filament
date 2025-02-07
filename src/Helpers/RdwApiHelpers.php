<?php

if (! function_exists('selectAllDatasets')) {

    function selectAllDatasets(?string $label = null)
    {
        return function (\Filament\Forms\Components\Select $component) use ($label) {
            return \Filament\Forms\Components\Actions\Action::make('selectall')
                ->label($label ?? __('rdw-api::form.selectallLabel'))
                ->icon('heroicon-m-list-bullet')
                ->action(function (\Filament\Forms\Set $set) use ($component) {
                    $component->state(array_keys($component->getEnabledOptions()));
                });
        };
    }
}
