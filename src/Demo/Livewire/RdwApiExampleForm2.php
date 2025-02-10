<?php

namespace Jdkweb\Rdw\Filament\Demo\Livewire;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Jdkweb\Rdw\Enums\Endpoints;
use Jdkweb\Rdw\Enums\OutputFormat;
use Jdkweb\Rdw\Controllers\RdwApiResponse;
use Jdkweb\Rdw\Filament\Forms\Components\RdwApiLicenseplate;

trait RdwApiExampleForm2
{
    /**
     * Async Small Form
     *
     * @param  Form  $form
     * @return Form
     */
    final protected function exampleForm2(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->extraAttributes(['style' => "margin: 40px"])
                ->schema([
                    RdwApiLicenseplate::make('kenteken')
                        ->default('HX-084-V')
                        ->label(__('rdw-api::form.licenseplateLabel'))
                        ->required()
                        ->dataSet([
                            Endpoints::VEHICLE,
                            Endpoints::FUEL,
                            Endpoints::AXLES
                        ])
                        //->forceTranslation('en')
                        ->outputFormat(fn() => OutputFormat::JSON)
                        ->licenseplateStyle()
                        ->live(true)
                        ->afterStateUpdated(function ($state, Forms\Set $set) use ($form) {

                            $result = \Jdkweb\Rdw\Filament\Controllers\RdwApiRequest::make()
                                ->setFormData($form)
                                ->rdwApiRequest()
                                ->get();


                            if ($result->status === false) {
                                return;
                            }

                            $set('merk', @$result->response['Voertuigen']['merk']);
                            $set('voertuigsoort', $result->quickSearch('voertuigsoort'));
                            $set('brandstof_omschrijving', $result->quickSearch('brandstof_omschrijving'));
                            $set('aslast', $result->quickSearch('wettelijk_toegestane_maximum_aslast', 0));
                        }),

                    Forms\Components\Fieldset::make('result')
                        ->label('Result')
                        ->schema([
                            TextInput::make('merk')
                                ->label(
                                    ucfirst(
                                        str_replace(
                                            "_",
                                            " ",
                                            __('rdw-api::vehicle.merk')
                                        )
                                    )
                                )
                                ->readonly(),
                            TextInput::make('voertuigsoort')
                                ->label(
                                    ucfirst(
                                        str_replace(
                                            "_",
                                            " ",
                                            __('rdw-api::vehicle.voertuigsoort')
                                        )
                                    )
                                )
                                ->readonly(),
                            TextInput::make('brandstof_omschrijving')
                                ->label(
                                    ucfirst(
                                        str_replace(
                                            "_",
                                            " ",
                                            __('rdw-api::fuel.brandstof_omschrijving')
                                        )
                                    )
                                )
                                ->readonly(),
                            TextInput::make('aslast')
                                ->label(
                                    ucfirst(
                                        str_replace(
                                            "_",
                                            " ",
                                            __('rdw-api::axles.wettelijk_toegestane_maximum_aslast')
                                        )
                                    )
                                )
                                ->readonly(),
                        ])
                ])
            ])->statePath('exampleForm2Data');
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     *  Filament form exampleForm2
     *
     * @param  RdwApiResponse $data
     * @return void
     */
    final public function exampleForm2Handler(RdwApiResponse $data):void
    {
        $this->exampleForm2->fill([
            'kenteken' => $data->request->licenseplate,
            'merk' =>  $data->quickSearch('merk') ?? '',
            'voertuigsoort' => $data->quickSearch('voertuigsoort') ?? '',
            'brandstof_omschrijving' => $data->quickSearch('brandstof_omschrijving') ?? '',
            'aslast' => $data->quickSearch('wettelijk_toegestane_maximum_aslast', 0) ?? '',
        ]);
    }
}
