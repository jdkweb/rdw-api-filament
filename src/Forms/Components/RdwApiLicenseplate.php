<?php

namespace Jdkweb\Rdw\Filament\Forms\Components;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Contracts\View\View;
use Illuminate\View\ComponentAttributeBag;
use Jdkweb\Rdw\Enums\Endpoints;
use Jdkweb\Rdw\Enums\OutputFormat;

class RdwApiLicenseplate extends TextInput
{
    public string $prefix = 'plate_';

    protected string $regexLicensePlate = "/(\w{2}-?\d{2}-?\d{2})|(\d{2}-?\d{2}-?\w{2})|(\d{2}-?\w{2}-?\d{2})|(\w{2}-?\d{2}-?\w{2})|(\w{2}-?\w{2}-?\d{2})|(\d{2}-?\w{2}-?\w{2})|(\d{2}-?\w{3}-?\d{1})|(\d{1}-?\w{3}-?\d{2})|(\w{2}-?\d{3}-?\w{1})|(\w{1}-?\d{3}-?\w{2})|(\w{3}-?\d{2}-?\w{1})|(\d{1}-?\w{2}-?\d{3})/i";

    protected string|\Closure $outputFormat = 'array';

    protected string|array|\Closure|null $dataset = null;

    protected string|\Closure|null $language = 'nl';

    // protected string $license_plate_tailwind_style = 'bg-yellow-300 font-bold border border-black border-l-[42px] border-l-blue-700';

    //private string|\Closure|null $useDatasetSelector = null;

    //public $parent;

    //protected RdwApiSelectDataset $selectDataset;


    protected function setUp(): void
    {
        $this->maxLength = 8;
        $this->minLength = 6;
        $this->regex($this->regexLicensePlate);
        $this->language = app()->getLocale();
        $this->dataset = Endpoints::names();

        parent::setUp();
    }


    /**
     * Set output format Json | array | xml
     *
     * @param  string|\Closure  $type
     * @return $this
     */
    public function outputFormat(string|\Closure $type = 'array'): static
    {
        $this->outputFormat = $type;
        return $this;
    }

    public function dataSet(string|array|\Closure|null $dataset = null): static
    {
        $this->dataset = $dataset;

        // check string
        if (is_string($dataset)) {
            $this->dataset = [$dataset];
        }

        // check string
        if (is_null($dataset) || empty($dataset) || is_array($dataset) && strtoupper($dataset[0]) == 'ALL') {
            $this->dataset = Endpoints::names();
        }

        // Accept lower key datasets
        $this->dataset = array_map(fn($value): string => strtoupper($value), $this->dataset);

        // Check endpoints
        if (count(array_diff($this->dataset, Endpoints::names())) != 0) {
            $this->dataset = Endpoints::names();
        }

        return $this;
    }

    /**
     * Set preset licenseplate styling
     * Can (be) overwrite with extra(Input)Attributes)
     *
     * @param  string  $type
     * @return $this
     */
    public function licenseplateStyle(string $type = 'default'): static
    {
        if ($type === 'default') {
            $this->extraInputAttributes([
                'class' => 'font-bold',
                'style' => 'background: #F3B701; font-size: 32px;letter-spacing: 2px; height: 48px'
            ]);
            $this->extraAttributes(['style' => 'border: 2px solid #241D0A; border-left: 48px solid #003CAA; border-radius: 5px;']);
        } elseif ($type === 'taxi') {
            $this->extraInputAttributes([
                'class' => 'font-bold',
                'style' => 'background: #05B0F0; font-size: 32px;letter-spacing: 2px; height: 48px'
            ]);
            $this->extraAttributes(['style' => 'border: 2px solid #241D0A; border-radius: 5px;']);
        }

        return $this;
    }

    /**
     * Set language for the formfields and the output
     *
     * @param  string|\Closure  $language
     * @return $this
     */
    public function language(string|\Closure $language = 'nl'): static
    {
        $this->language = $language;
        return $this;
    }

    public function getLanguage(): string
    {
        return $this->evaluate($this->language) ?? app()->getLocale();
    }

//    public function showSelectDataset(array|\Closure|null $settings = null): static
    //    {
    //
    //        $this->showSelectDataset = RdwApiSelectDataset::make('dataset')
    //            ->label('Select dataset.')
    //            ->dataSet('all')            // empty '' [] "VEHICLE", ["VEHICLE","FUEL"] 'all' ['ALL']
    //            ->shortname(false)
    //            ->showSelectAll('Select All')
    ////                        ->showClear()
    ////                        ->live(onBlur: true)
    //            ->multiple(true);
    //
    //
    //        return $this;
    //    }

//    /**
//     * Show optionMenu to select DataSets
//     *
//     * @param  string|\Closure|null  $selector
//     * @return $this
//     */
//    public function useDatasetSelectorName(string|\Closure|null $selector): static
//    {
//        $this->useDatasetSelector = $selector;
//
//        return $this;
//    }
//
//    public function getUseDatasetSelectorName(): string
//    {
//        return $this->evaluate($this->useDatasetSelector);
//    }


    public function responseHandler(mixed $value, array $namedInjections = [], array $typedInjections = []): static
    {
        if (! $value instanceof \Closure) {
            return $value;
        }

        $dependencies = [];
        $result_return_function_name = null;

        foreach ((new \ReflectionFunction($value))->getParameters() as $parameter) {
            if(!in_array($parameter->getName(), ['state','set','get','component', 'record', 'livewire', 'operation','request'])) {
                $result_return_function_name = $parameter->getName();
                continue;
            }
            if(!in_array($parameter->getName(), ['set','get'])) {
                continue;
            }
            $dependencies[] = $this->resolveClosureDependencyForEvaluation($parameter, $namedInjections, $typedInjections);
        }

        if (is_string($this->dataset)) {
            $this->dataset = [$this->dataset];
        }

        $this->live(true)
            ->afterStateUpdated(function (RdwApiLicenseplate $component, string $state) use ($value, $dependencies, $result_return_function_name) {
                $dependencies[$result_return_function_name] = $this->createResponseObject($component, $state);
                return $value(...$dependencies);
            });

        return $this;
    }

    /**
     * Create Rdw-Api response object
     *
     * @param  array  $data
     * @return void
     */
    private function createResponseObject(RdwApiLicenseplate $component, string $licenseplate): RdwApiResponse
    {
        $endpoints = $this->getDataset();
        $format = $this->getOutputFormat();

        $response = new RdwApiResponse();
        $response->language = $this->getLanguage();
        $response->state = $licenseplate;
        $response->format = $format;
        $response->endpoints = $endpoints;
        $response->response = $this->rdwApiRequest($licenseplate, $endpoints, $format);
        $response->ok = (count($response->response) == 0 ? false : true);

        return $response;
    }

    private function rdwApiRequest(string $licenseplate, array $endpoints, string $format): array
    {
        // Is options SelectDataset used
        //        if (isset($component?->sibling) && !is_null($component?->sibling)) {
        //            $endpoints = $get($component->sibling->getName()) ?? $this->getDataset();
        //        } else {
        //
        //            $endpoints = $this->getDataset();
        //        }

        return \Jdkweb\Rdw\Facades\Rdw::finder()
            ->setLicense($licenseplate)
            ->setEndpoints($endpoints)
            ->translate($this->getLanguage())
            ->convert($format)
            ->fetch();

//        return $component->parentComponent->rdwApiRequestHandler(
//            $licenseplate,
//            $endpoints,
//            $this->getLanguage(),
//            $this->getOutputFormat()
//        );
    }

    public function getOutputFormat(): string
    {
        $format = $this->evaluate($this->outputFormat);
        if (!in_array($format, OutputFormat::names())) {
            $format = 'array';
        }

        return $format;
    }

    public function getDataset(): array|string
    {
        return $this->evaluate($this->dataset);
    }
}
