<?php

namespace Jdkweb\RdwApi\Filament\Demo\Livewire;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Jdkweb\RdwApi\Filament\Enums\Endpoints;
use Jdkweb\RdwApi\Filament\Enums\OutputFormat;
use Jdkweb\RdwApi\Controllers\RdwApiResponse;
use Jdkweb\RdwApi\Filament\Forms\Components\RdwApiLicenseplate;

trait RdwApiExampleForm2
{
    /**
     * Async Small Form
     *
     * @param  Form  $form
     * @return Form
     */
    protected function exampleForm2(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->extraAttributes(['style' => "margin: 40px"])
                ->schema([
                    RdwApiLicenseplate::make('kenteken')
                        //->setApi(0)
                        ->label(__('rdw-api::form.licenseplateLabel'))
                        ->required()
                        ->setEndpoints([
                            Endpoints::VEHICLE,
                            Endpoints::FUEL,
                            Endpoints::AXLES
                        ])
                        ->setOutputformat(fn() => OutputFormat::JSON)
                        ->licenseplateStyle()
                        ->live(true)
                        ->afterStateUpdated(function ($state, Forms\Set $set) use ($form) {

                            $result = \Jdkweb\RdwApi\Controllers\RdwApiRequest::make()
                                ->setFormData($form)
                                //->setApi(0)
                                ->fetch();

                            if ($result->status === false) {
                                return;
                            }

                            $set('merk', @$result->response['Voertuigen']['merk']);
                            $set('voertuigsoort', $result->quickSearch('voertuigsoort'));
                            // first fuel when hybrid
                            $set('brandstof_omschrijving', $result->quickSearch('1.brandstof_omschrijving'));
                            // second axle
                            $set('aslast', $result->quickSearch('2.wettelijk_toegestane_maximum_aslast'));
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
    public function exampleForm2Handler(RdwApiResponse $data):void
    {
        $this->exampleForm2->fill([
            'kenteken' => $data->request->licenseplate,
            'merk' =>  $data->quickSearch('merk') ?? '',
            'voertuigsoort' => $data->quickSearch('voertuigsoort') ?? '',
            'brandstof_omschrijving' => $data->quickSearch('1.brandstof_omschrijving') ?? '',
            'aslast' => $data->quickSearch('2.wettelijk_toegestane_maximum_aslast') ?? '',
        ]);
    }
}
