<?php

namespace Jdkweb\RdwApi\Filament\Forms\Components;

use Filament\Forms\Components\TextInput;
use Jdkweb\RdwApi\Controllers\RdwApiRequest;
use Jdkweb\RdwApi\Controllers\RdwApiResponse;
use Jdkweb\RdwApi\Enums\Endpoints;
use Jdkweb\RdwApi\Filament\Enums\OutputFormat;

class RdwApiLicenseplate extends TextInput
{
    protected string $regexLicensePlate = "/(\w{2}-?\d{2}-?\d{2})|(\d{2}-?\d{2}-?\w{2})|
    (\d{2}-?\w{2}-?\d{2})|
    (\w{2}-?\d{2}-?\w{2})|(\w{2}-?\w{2}-?\d{2})|
    (\d{2}-?\w{2}-?\w{2})|(\d{2}-?\w{3}-?\d{1})|
    (\d{1}-?\w{3}-?\d{2})|(\w{2}-?\d{3}-?\w{1})|
    (\w{1}-?\d{3}-?\w{2})|(\w{3}-?\d{2}-?\w{1})|
    (\d{1}-?\w{2}-?\d{3})/i";

    protected string|int|\Closure $api = 0;

    protected string|OutputFormat|\Closure|null $outputFormat = '';

    protected string|array|Endpoints|\Closure $endpoints = [];

    protected string|\Closure|null $language = 'nl';

    //------------------------------------------------------------------------------------------------------------------

    protected function setUp(): void
    {
        $this->maxLength = 8; // exp. 52-BVL-9
        $this->minLength = 6; // exp. 52BVL9
        $this->regex($this->regexLicensePlate);
        $this->language = app()->getLocale();
        $this->endpoints = Endpoints::cases(); //['VEHICLE','VEHICLE_CLASS','FUEL','BODYWORK','BODYWORK_SPECIFIC','AXLES']

        parent::setUp();
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Create Rdw-Api response object for onBlur
     *
     * @param  array  $data
     * @return void
     */
    private function rdwApiRequest(string $licenseplate): RdwApiResponse   //RdwApiLicenseplate $component,
    {
        $endpoints = $this->getEndpoints();
        $format = $this->getOutputformat();

        $response = RdwApiRequest::make()
            ->setAPI($this->getApi())
            ->setLanguage($this->getLanguage())
            ->setLicenseplate($licenseplate)
            ->setOutputformat($format)
            ->setEndpoints($endpoints)
            ->fetch();

        return $response;
    }

    //------------------------------------------------------------------------------------------------------------------

    public function setApi(string|int|\Closure $api): static
    {
        $this->api = $api;
        return $this;
    }

    //------------------------------------------------------------------------------------------------------------------

    public function getApi(): string|int
    {
        return $this->evaluate($this->api);
    }


    //------------------------------------------------------------------------------------------------------------------

    public function setEndpoints(string|array|Endpoints|\Closure $endpoints): static
    {
        if(is_string($endpoints)) {
            $endpoints = [$endpoints];
        }

        $this->endpoints = $endpoints;
        return $this;
    }

    //------------------------------------------------------------------------------------------------------------------

    public function getEndpoints(): array
    {
        $this->endpoints = $this->evaluate($this->endpoints);

        // check default settings
        foreach ($this->endpoints as $key => $endpoints) {
            if ($endpoints instanceof Endpoints) {
                $this->endpoints[$key] = $endpoints;
            }
        }

        return $this->endpoints;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Set output format Json | array | xml
     *
     * @param  string|OutputFormat|\Closure  $type
     * @return $this
     */
    public function setOutputformat(string|OutputFormat|\Closure $type = 'array'): static
    {
        $this->outputFormat = $type;
        return $this;
    }

    //------------------------------------------------------------------------------------------------------------------

    public function getOutputformat(): ?OutputFormat
    {
        $format = $this->evaluate($this->outputFormat);

        // check after evaluate
        if (!in_array($format, OutputFormat::cases())) {
            $format = null; //OutputFormat::ARRAY;
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
    public function setLanguage(string|\Closure|null $language = null): static
    {
        $this->language = $language ?? app()->getLocale();
        return $this;
    }

    //------------------------------------------------------------------------------------------------------------------

    public function getLanguage(): string
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
    public function licenseplateStyle(string $type = 'default'): static
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
