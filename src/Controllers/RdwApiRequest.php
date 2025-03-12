<?php

namespace Jdkweb\RdwApi\Filament\Controllers;

use Filament\Forms\Form;
use Jdkweb\RdwApi\Exceptions\RdwException;
use Jdkweb\RdwApi\Filament\Enums\Endpoints;
use Jdkweb\RdwApi\Enums\Interface\Endpoint;
use Jdkweb\RdwApi\Filament\Forms\Components\RdwApiLicenseplate;
use Jdkweb\RdwApi\Controllers\RdwApiRequest as BaseController;

class RdwApiRequest extends BaseController
{

    public static function make(): static
    {
        // Singleton
        if (is_null(self::$instance)) {
            self::$instance = new self();
            // Default settings
            self::$instance->endpoints = Endpoints::cases();
            self::$instance->language = app()->getLocale();
        }

        return self::$instance;
    }

    /**
     * Endpoints used for the request
     *
     * @param  array  $endpoints
     * @return $this
     */
    public function setEndpoints(array $endpoints = []): static
    {
        $this->endpoints = array_map(function ($endpoint) {
            return ($endpoint instanceof Endpoint ? $endpoint : Endpoints::getCase($endpoint));
        }, $endpoints);
        return $this;
    }

    /**
     * Get settings from the filament form
     *
     * @param  Form  $form
     * @return $this
     * @throws RdwException
     */
    public function setFormData(Form $form): static
    {
        $data = $form->getState();
        $rdwApiLicenseplate = $this->getComponent($form);
        $licensePlateName = $rdwApiLicenseplate->getStatePath(false);

        // Set data for RDW request
        $this->licenseplate = $data[$licensePlateName];
        $this->outputformat = $rdwApiLicenseplate->getOutputformat();
        $this->language = $rdwApiLicenseplate->getLanguage();
        $this->api = $rdwApiLicenseplate->getApi();

        return $this->setEndpoints($rdwApiLicenseplate->getEndpoints());
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Get filament component
     *
     * @throws RdwException
     */
    private function getComponent(Form $form): RdwApiLicenseplate
    {
        $components = $form->getFlatComponents();

        $licenseplate = null;

        foreach ($components as $component) {
            if ($component instanceof RdwApiLicenseplate) {
                $licenseplate =& $component;
                break;
            }
        }

        if (is_null($licenseplate)) {
            throw new RdwException(__('rdw-api::errors.component_error', [
                'class' => self::class,
                'component' => RdwApiLicenseplate::class
            ]));
        }

        return $licenseplate;
    }
}
