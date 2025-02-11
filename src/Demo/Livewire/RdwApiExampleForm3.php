<?php

namespace Jdkweb\RdwApi\Filament\Demo\Livewire;

use Filament\Forms;
use Filament\Forms\Form;
use Jdkweb\RdwApi\Enums\Endpoints;
use Jdkweb\RdwApi\Enums\OutputFormat;
use Jdkweb\RdwApi\Controllers\RdwApiResponse;
use Jdkweb\RdwApi\Filament\Forms\Components\RdwApiLicenseplate;

trait RdwApiExampleForm3
{
    /**
     * Async Alt Form
     *
     * @param  Form  $form
     * @return Form
     */
    protected function exampleForm3(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->extraAttributes(['style' => "margin: 40px"])
                    ->schema([
                        RdwApiLicenseplate::make('taxiplate')
                            ->label(__('rdw-api::form.licenseplateLabel'))
                            ->setEndpoints(fn(Forms\Get $get): array => $get('taxidataset_disabled'))
                            ->setOutputformat(fn(Forms\Get $get): OutputFormat => $get('output_disabled'))
                            ->required()
                            ->setLanguage(fn($get) => app()->getLocale())
                            ->licenseplateStyle('taxi')
                            ->live(true)
                            ->afterStateUpdated(function ($state, Forms\Set $set) use ($form) {

                                $result = \Jdkweb\RdwApi\Filament\Controllers\RdwApiRequest::make()
                                    ->setFormData($form)
                                    ->rdwApiRequest()
                                    ->get();

                                if ($result->status === false) {
                                    return;
                                }

                                // Render result
                                $this->livewireOutput($result);
                            }),
                        Forms\Components\Select::make('taxidataset_disabled')
                            ->label(__('rdw-api::form.selectdatasetLabel'))
                            ->multiple()
                            ->options(Endpoints::class)
                            ->default([
                                Endpoints::VEHICLE,
                                Endpoints::FUEL,
                            ])
                            ->reactive()
                            ->disabled(true)
                            ->required(),
                        Forms\Components\Select::make('output_disabled')
                            ->label(__('rdw-api::form.formatLabel'))
                            ->disabled(true)
                            ->required()
                            ->default(OutputFormat::XML)
                            ->options(OutputFormat::class)
                            ->reactive(),
                    ])
            ])->statePath('exampleForm3Data');
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Filament form exampleForm3
     *
     * @param  array  $data
     * @return array|string
     */
    public function exampleForm3Handler(RdwApiResponse $data): void
    {
        // handle data
    }
}
