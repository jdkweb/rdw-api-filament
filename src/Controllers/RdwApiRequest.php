<?php

namespace Jdkweb\RdwApi\Filament\Controllers;

use Filament\Forms\Form;
use Jdkweb\RdwApi\Exceptions\RdwException;
use Jdkweb\RdwApi\Filament\Forms\Components\RdwApiLicenseplate;

class RdwApiRequest
{
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
