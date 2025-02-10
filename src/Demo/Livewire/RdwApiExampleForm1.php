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
    protected function exampleForm1(Form $form): Form
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
                            ->reactive()
                            ->required(),
                        RdwApiLicenseplate::make('licenseplate')
                            ->label(__('rdw-api::form.licenseplateLabel'))
                            ->setOutputformat(fn(Forms\Get $get) => $get('output_format'))
                            ->setEndpoints(fn(Forms\Get $get) => $get('datasets'))
                            ->setEndpoints(Endpoints::cases())
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
    public function exampleForm1Handler(RdwApiResponse $data): void
    {
        // handle data
    }
}
