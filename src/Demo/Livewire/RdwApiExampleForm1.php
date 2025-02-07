<?php

namespace Jdkweb\Rdw\Filament\Demo\Livewire;

use Filament\Forms;
use Filament\Forms\Form;
use Jdkweb\Rdw\Enums\Endpoints;
use Jdkweb\Rdw\Enums\OutputFormat;
use Jdkweb\Rdw\Controllers\RdwApiResponse;
use Jdkweb\Rdw\Filament\Forms\Components\RdwApiLicenseplate;

trait RdwApiExampleForm1
{
    /**
     * Async Full Form
     *
     * @param  Form  $form
     * @return Form
     */
    final protected function exampleForm1(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->extraAttributes(['style' => "margin: 40px"])
                    ->schema([
                        Forms\Components\Select::make('datasets')
                            ->label(__('rdw-api::form.selectdatasetLabel'))
                            ->multiple()
                            ->options(Endpoints::class)
                            ->default([
                                Endpoints::VEHICLE,
                                Endpoints::FUEL
                            ])
                            ->hintAction(selectAllDatasets())

//                            ->hintAction(function (Forms\Components\Select $component) {
//                                return \Filament\Forms\Components\Actions\Action::make('selectall')
//                                    ->label(__('rdw-api::form.selectallLabel'))
//                                    ->icon('heroicon-m-list-bullet')
//                                    ->action(function (Set $set) use ($component) {
//                                        $component->state(array_keys($component->getEnabledOptions()));
//                                    });
//                            })

                            ->reactive()
                            ->required(),
                        RdwApiLicenseplate::make('licenseplate')
                            ->label(__('rdw-api::form.licenseplateLabel'))
                            ->default('HX084V')
                            ->outputFormat(fn(Forms\Get $get) => $get('output_format'))
                            ->dataSet(fn(Forms\Get $get) => $get('datasets'))
                            ->required()
                            ->licenseplateStyle(),
                        Forms\Components\Select::make('output_format')
                            ->label(__('rdw-api::form.formatLabel'))
                            ->required()
                            ->default(OutputFormat::XML)
                            ->options(OutputFormat::class)
                            ->reactive() // Enables reactivity
                    ])
            ])->statePath('exampleForm1Data');
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Filament form exampleForm1
     *
     * @param  RdwApiResponse  $data
     * @return array|string
     */
    final public function exampleForm1Handler(RdwApiResponse $data): void
    {
        // handle data
    }
}
