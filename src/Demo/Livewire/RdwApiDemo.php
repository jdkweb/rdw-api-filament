<?php

namespace Jdkweb\Rdw\Filament\Demo\Livewire;

use Filament\Forms\Components\TextInput;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Jdkweb\Rdw\Enums\Endpoints;
use Jdkweb\Rdw\Enums\OutputFormat;
use Jdkweb\Rdw\Filament\Forms\Components\RdwApiLicenseplate;
use Jdkweb\Rdw\Filament\Forms\Components\RdwApiResponse;
use Jdkweb\Rdw\Filament\Forms\Components\RdwApiSelectDataset;
use Livewire\Component;
use Illuminate\Contracts\View\View;

class RdwApiDemo extends Component implements HasForms
{
    use InteractsWithForms;

    public string | array | null $livewire_results = null;

    public string | array | null $results = null;

    public ?array $exampleForm1Data = [];
    public ?array $exampleForm2Data = [];
    public ?array $exampleForm3Data = [];

    /**
     * Rdw API request
     * data for request
     * @var string
     */
    protected string $licenseplate;
    protected array  $endpoints = [];
    protected string $output_format;
    protected string $language;

    protected $layout = 'rdw_views::components.layouts.rdw-api-demo';


    public function __construct()
    {
        $this->setLanguage();

        // views from rdw-api adn rdw-api-filament
        view()->addNamespace('rdw_views', [
                dirname(dirname(dirname(dirname(__DIR__)))) . '/rdw-api/src/Demo/views/',
                dirname(__DIR__) . '/views/'
            ]);
    }

    /**
     * Set language with part of the uri
     *
     * @return void
     */
    protected function setLanguage():void
    {
        $url = config('rdw-api.rdw_api_folder') . "/". config('rdw-api.rdw_api_filament_folder') . "/". config('rdw-api.rdw_api_demo_slug');

        $language = app()->getLocale();
        if(preg_match("/^" . addcslashes($url,"/") . "\/(nl|en)$/", request()->path())) {
            $language = str_replace($url."/","",request()->path());
        }
        app()->setLocale($language);
    }

    public function mount()
    {
        $this->exampleForm1->fill();
        $this->exampleForm2->fill();
        $this->exampleForm3->fill();
    }

    protected function getForms(): array
    {
        return [
            'exampleForm1',
            'exampleForm2',
            'exampleForm3',
        ];
    }

    protected function exampleForm1(Form $form)
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->extraAttributes(['style' => "margin: 40px"])
                    ->schema([
                        RdwApiSelectDataset::make('datasets')
                            ->label(__('rdw-api::form.selectdatasetLabel'))
                            ->multiple()
                            ->dataSet('all')
                            ->setDefault(fn() => ['vehicle'])
                            ->shortname(false)
                            ->showSelectAll('Select Alles')
                            ->required(),
                        RdwApiLicenseplate::make('licenseplate')
                            ->label(__('rdw-api::form.licenseplateLabel'))
                            ->default('HX084V')
                            ->required()
                            ->licenseplateStyle(),
                        Forms\Components\Select::make('output_format')
                            ->label(__('rdw-api::form.formatLabel'))
                            ->required()
                            ->options(OutputFormat::getOptions())
                    ])
            ])->statePath('exampleForm1Data');
    }

    protected function exampleForm2(Form $form)
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->extraAttributes(['style' => "margin: 40px"])
                ->schema([
                    RdwApiLicenseplate::make('kenteken')
                        ->default('HX084B')
                        ->label(__('rdw-api::form.licenseplateLabel'))
                        ->required()
                        ->dataSet(['vehicle','fuel'])
                        //->language(app()->getLocale())
                        ->outputFormat(fn() => 'array')
                        ->licenseplateStyle()
                        ->responseHandler(function (Forms\Get $get, Forms\Set $set, RdwApiResponse $resultObject)  {
                            if($resultObject->ok === false) return;

                            $set('merk', $resultObject->get(Endpoints::VEHICLE,'merk'));
                            $set('voertuigsoort', $resultObject->get(Endpoints::VEHICLE,'voertuigsoort'));
                            $set('brandstof_omschrijving', $resultObject->get(Endpoints::FUEL,'brandstof_omschrijving'));
                            $set('catalogusprijs', $resultObject->get(Endpoints::VEHICLE,'catalogusprijs'));

                            //$set('merk', $resultObject->VEHICLE[__('rdw-api::vehicle.merk')] ?? '');
                            //$set('voertuigsoort', $resultObject->VEHICLE[__('rdw-api::vehicle.voertuigsoort')] ?? '');
                            //$set('brandstof_omschrijving', $resultObject->FUEL[__('rdw-api::fuel.brandstof_omschrijving')] ?? '');
                            //$set('catalogusprijs', $resultObject->VEHICLE[__('rdw-api::vehicle.catalogusprijs')] ?? '');

                        }),
                    Forms\Components\Fieldset::make('result')
                        ->label('Result')
                        ->schema([
                            TextInput::make('merk')
                                ->label(ucfirst(str_replace("_"," ",__('rdw-api::vehicle.merk'))))
                                ->readonly(),
                            TextInput::make('voertuigsoort')
                                ->label(ucfirst(str_replace("_"," ",__('rdw-api::vehicle.voertuigsoort'))))
                                ->readonly(),
                            TextInput::make('brandstof_omschrijving')
                                ->label(ucfirst(str_replace("_"," ",__('rdw-api::fuel.brandstof_omschrijving'))))
                                ->readonly(),
                            TextInput::make('catalogusprijs')
                                ->label(ucfirst(str_replace("_"," ",__('rdw-api::vehicle.catalogusprijs'))))
                                ->readonly(),
                        ])
                ])
            ])->statePath('exampleForm2Data');
    }

    protected function exampleForm3(Form $form)
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->extraAttributes(['style' => "margin: 40px"])
                    ->schema([
                        RdwApiLicenseplate::make('taxiplate')
                            ->default('HX084V')
                            ->label(__('rdw-api::form.licenseplateLabel'))
                            ->required()
                            ->language(fn($get) => app()->getLocale())
                            ->licenseplateStyle('taxi')
                            ->responseHandler(function (Forms\Get $get, Forms\Set $set, RdwApiResponse $resultObject)  {
                                if($resultObject->ok === false) return;
                                $this->results = $resultObject->response;
                            }),
                        RdwApiSelectDataset::make('taxidataset_disabled')
                            ->label(__('rdw-api::form.selectdatasetLabel'))
                            ->multiple()
                            ->dataSet(['vehicle','fuel'])
                            ->setDefault(fn() => ['vehicle','fuel'])
                            ->shortname(false)
                            ->disabled(true),
                        Forms\Components\Hidden::make('taxidataset')
                            ->default(['vehicle','fuel']),
                        Forms\Components\Select::make('output_disabled')
                            ->label(__('rdw-api::form.formatLabel'))
                            ->default(OutputFormat::XML->name)
                            ->disabled(true)
                            ->options(OutputFormat::getOptions()),
                        Forms\Components\Hidden::make('output')
                            ->default(OutputFormat::XML->name)
                    ])
            ])->statePath('exampleForm3Data');
    }

    /**
     * Handle submitted Filament form
     *
     * @param  string  $form
     * @return void
     */
    public function handleForm(string $form): void
    {
        $data = $this->{$form}->getState();

        if(method_exists($this, $form)) {
             $response = $this->{$form."Handler"}($data);
        }

        $this->livewireOutput($response);
    }

    /**
     * Filament form exampleForm1
     *
     * @param  array  $data
     * @return array|string
     */
    public function exampleForm1Handler(array $data): array|string
    {
        $this->language = app()->getLocale();
        $this->licenseplate = $data['licenseplate'];
        $this->endpoints = $data['datasets'];
        $this->output_format = $data['output_format'];

        return $this->rdwRequest();
    }

    /**
     *  Filament form exampleForm2
     *
     * @param  array  $data
     * @return array|string
     */
    public function exampleForm2Handler(array $data): array|string
    {
        $this->language = app()->getLocale();
        $this->licenseplate = $data['kenteken'];
        $this->endpoints = ['VEHICLE','FUEL'];
        $this->output_format = 'array';

        $result = $this->rdwRequest();

        $this->exampleForm2->fill([
            'kenteken' => $data['kenteken'],
            'merk' => $result[__('rdw-api::enums.VEHICLE')][__('rdw-api::vehicle.merk')] ?? '',
            'voertuigsoort' => $result[__('rdw-api::enums.VEHICLE')][__('rdw-api::vehicle.voertuigsoort')] ?? '',
            'inrichting' => $result[__('rdw-api::enums.VEHICLE')][__('rdw-api::vehicle.inrichting')] ?? '',
            'brandstof_omschrijving' => $result[__('rdw-api::enums.FUEL')][__('rdw-api::fuel.brandstof_omschrijving')] ?? ''
        ]);

        return $result;
    }

    /**
     * Filament form exampleForm1
     *
     * @param  array  $data
     * @return array|string
     */
    public function exampleForm3Handler(array $data): array|string
    {
        $this->language = app()->getLocale();
        $this->licenseplate = $data['taxiplate'];
        $this->endpoints = $data['taxidataset'];
        $this->output_format = $data['output'];

        return $this->rdwRequest();
    }

    /**
     * Call to Rdw API
     *
     * @return array|string
     */
    protected function rdwRequest(): array|string
    {
        return \Jdkweb\Rdw\Facades\Rdw::finder()
            ->setLicense($this->licenseplate)
            ->setEndpoints($this->endpoints)
            ->translate($this->language)
            ->convert($this->output_format)
            ->fetch();
    }

    /**
     * Create output for livewire form
     *
     * @param  string|array  $response
     * @return void
     */
    protected function livewireOutput(string|array $response): void
    {
        if($this->output_format === OutputFormat::XML->name) {
            $this->livewire_results = view('rdw_views::components.xml', ['results' => $this->formatXml($response)]);
        }
        elseif($this->output_format === OutputFormat::JSON->name) {
            $this->livewire_results = view('rdw_views::components.json', ['noscript' => true]);
            $this->dispatch('getJsonResult', result: stripslashes($response));
        }
        else {
            $this->livewire_results = view('rdw_views::components.array', ['results' => $response]);
        }

    }

    /**
     * Clear by tab switch
     *
     * @return void
     */
    public function clear()
    {
        $this->results = null;
        $this->livewire_results = null;
    }

    /**
     * Readable xml
     * @param  string  $result
     * @return string
     */
    protected function formatXml(string $result): string
    {
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($result);
        return htmlentities($dom->saveXML($dom->documentElement));
    }

    public function render(): View
    {
        // Pass not livewire data for the post form
        return view('rdw_views::livewire.rdw-api-demo')
            ->layout($this->layout, [
                'language' => app()->getLocale(),
            ]);
    }
}
