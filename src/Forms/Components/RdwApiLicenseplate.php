<?php

namespace Jdkweb\Rdw\Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\TextInput;
use Jdkweb\Rdw\Controllers\RdwApiRequest;
use Jdkweb\Rdw\Controllers\RdwApiResponse;
use Jdkweb\Rdw\Enums\Endpoints;
use Jdkweb\Rdw\Enums\OutputFormat;


class RdwApiLicenseplate extends TextInput
{
    protected string $regexLicensePlate = "/(\w{2}-?\d{2}-?\d{2})|(\d{2}-?\d{2}-?\w{2})|(\d{2}-?\w{2}-?\d{2})|(\w{2}-?\d{2}-?\w{2})|(\w{2}-?\w{2}-?\d{2})|(\d{2}-?\w{2}-?\w{2})|(\d{2}-?\w{3}-?\d{1})|(\d{1}-?\w{3}-?\d{2})|(\w{2}-?\d{3}-?\w{1})|(\w{1}-?\d{3}-?\w{2})|(\w{3}-?\d{2}-?\w{1})|(\d{1}-?\w{2}-?\d{3})/i";

    protected string|OutputFormat|\Closure $outputFormat = 'array';

    protected string|array|Endpoints|\Closure $dataset = [];

    protected string|\Closure|null $language = 'nl';

    //------------------------------------------------------------------------------------------------------------------

    final protected function setUp(): void
    {
        $this->maxLength = 8;                   // 52-BVL-9
        $this->minLength = 6;                   // 52BVL9
        $this->regex($this->regexLicensePlate);
        $this->language = app()->getLocale();
        $this->dataset = Endpoints::cases();    // ['VEHICLE','VEHICLE_CLASS','FUEL','BODYWORK','BODYWORK_SPECIFIC','AXLES']

        parent::setUp();
    }

    //------------------------------------------------------------------------------------------------------------------

    final public function dataSet(string|array|Endpoints|\Closure|null $dataset = null): static
    {
        $this->dataset = $dataset;
        return $this;
    }

    //------------------------------------------------------------------------------------------------------------------

    final public function getDataset(): array
    {
        $this->dataset = $this->evaluate($this->dataset);

        // check default settings
        foreach ($this->dataset as $key => $dataset) {
            if($dataset instanceof Endpoints) {
                $this->dataset[$key] = $dataset;
            }
        }

        return $this->dataset;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Preset afterStateUpdated
     *
     * ->responseHandler(function (Forms\Get $get, Forms\Set $set, RdwApiResponse $resultObject) {...})
     *
     * @param  mixed  $value
     * @param  array  $namedInjections
     * @param  array  $typedInjections
     * @return $this
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \ReflectionException
     */
//    final public function responseHandler(mixed $value, array $namedInjections = [], array $typedInjections = []): static
//    {
//        if (! $value instanceof \Closure) {
//            return $value;
//        }
//
//        $dependencies = [];
//        $result_return_function_name = null;
//
//        foreach ((new \ReflectionFunction($value))->getParameters() as $parameter) {
//            if(!in_array($parameter->getName(), ['state','set','get','component', 'record', 'livewire', 'operation','request'])) {
//                $result_return_function_name = $parameter->getName();
//                continue;
//            }
//            if(!in_array($parameter->getName(), ['set','get'])) {
//                continue;
//            }
//            $dependencies[] = $this->resolveClosureDependencyForEvaluation($parameter, $namedInjections, $typedInjections);
//        }
//
//        if (is_string($this->dataset)) {
//            $this->dataset = [$this->dataset];
//        }
//
//        $this->live(true)
//            ->afterStateUpdated(function (string $state) use ($value, $dependencies, $result_return_function_name) {
//                $dependencies[$result_return_function_name] = $this->rdwApiRequest($state);
//                return $value(...$dependencies);
//            });
//
//        return $this;
//    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Create Rdw-Api response object for onBlur
     *
     * @param  array  $data
     * @return void
     */
    private function rdwApiRequest(string $licenseplate): RdwApiResponse   //RdwApiLicenseplate $component,
    {
        $endpoints = $this->getDataset();
        $format = $this->getOutputFormat();

        $response = RdwApiRequest::make()
            ->setLanguage($this->getLanguage())
            ->setLicenseplate($licenseplate)
            ->setOutputFormat($format)
            ->setEndpoints($endpoints)
            ->rdwApiRequest()
            ->get();

        return $response;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Set output format Json | array | xml
     *
     * @param  string|OutputFormat|\Closure  $type
     * @return $this
     */
    final public function outputFormat(string|OutputFormat|\Closure $type = 'array'): static
    {
        $this->outputFormat = $type;
        return $this;
    }

    //------------------------------------------------------------------------------------------------------------------

    final public function getOutputFormat(): OutputFormat
    {
        $format = $this->evaluate($this->outputFormat);

        // check after evaluate
        if (!in_array($format, OutputFormat::cases())) {
            $format = OutputFormat::ARRAY;
        }

        return $format;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Set language to force specific output language in
     * opposite to local or webpage language
     *
     * @param  string|\Closure  $language
     * @return $this
     */
    final public function forceTranslation(string|\Closure|null $language = null): static
    {
        $this->language = $language ?? app()->getLocale();
        return $this;
    }

    //------------------------------------------------------------------------------------------------------------------

    final public function getLanguage(): string
    {
        return $this->evaluate($this->language) ?? app()->getLocale();
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Set preset licenseplate styling
     * Can (be) overwrite with extra(Input)Attributes)
     *
     * @param  string  $type
     * @return $this
     */
    final public function licenseplateStyle(string $type = 'default'): static
    {
        if ($type === 'default') {
            $this->extraInputAttributes([
                'class' => 'font-bold',
                'style' => 'background: #F3B701; font-size: 32px;letter-spacing: 2px; height: 48px'
            ]);
            $this->extraAttributes([
                'style' => 'border: 2px solid #241D0A; border-left: 48px solid #003CAA; border-radius: 5px;'
            ]);
        } elseif ($type === 'taxi') {
            $this->extraInputAttributes([
                'class' => 'font-bold',
                'style' => 'background: #05B0F0; font-size: 32px;letter-spacing: 2px; height: 48px'
            ]);
            $this->extraAttributes(['style' => 'border: 2px solid #241D0A; border-radius: 5px;']);
        }

        return $this;
    }
}
