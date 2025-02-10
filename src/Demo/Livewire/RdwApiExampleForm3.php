<?php

namespace Jdkweb\Rdw\Filament\Demo\Livewire;

use Filament\Forms;
use Filament\Forms\Form;
use Jdkweb\Rdw\Enums\Endpoints;
use Jdkweb\Rdw\Enums\OutputFormat;
use Jdkweb\Rdw\Controllers\RdwApiResponse;
use Jdkweb\Rdw\Filament\Forms\Components\RdwApiLicenseplate;

trait RdwApiExampleForm3
{
    /**
     * Async Alt Form
     *
     * @param  Form  $form
     * @return Form
     */
    final protected function exampleForm3(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->extraAttributes(['style' => "margin: 40px"])
                    ->schema([
                        RdwApiLicenseplate::make('taxiplate')
                            ->default('HX084V')
                            ->label(__('rdw-api::form.licenseplateLabel'))
                            ->dataSet(fn(Forms\Get $get): array => $get('taxidataset_disabled'))
                            ->outputFormat(fn(Forms\Get $get): OutputFormat => $get('output_disabled'))
                            ->required()
                            ->forceTranslation(fn($get) => app()->getLocale())
                            ->licenseplateStyle('taxi')
                            ->live(true)
                            ->afterStateUpdated(function ($state, Forms\Set $set) use ($form) {

                                $result = \Jdkweb\Rdw\Filament\Controllers\RdwApiRequest::make()
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
    final public function exampleForm3Handler(RdwApiResponse $data): void
    {
        // handle data
    }
}
