<?php

namespace Jdkweb\Rdw\Filament\Controllers;

use Filament\Forms\Form;
use Jdkweb\Rdw\Exceptions\RdwException;
use Jdkweb\Rdw\Filament\Forms\Components\RdwApiLicenseplate;

class RdwApiRequest extends \Jdkweb\Rdw\Controllers\RdwApiRequest
{
    /**
     * @var \Jdkweb\Rdw\Filament\Controllers\RdwApiRequest|null
     */
    private static RdwApiRequest|null $instance = null;

    /**
     * Overwrite parent class
     *
     * @return static
     */
    public static function make(): static
    {
        // Singleton
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Get settings from the filament form
     *
     * @param  Form  $form
     * @return $this
     * @throws RdwException
     */
    final public function setFormData(Form $form): static
    {
        $data = $form->getState();
        $rdwApiLicenseplate = $this->getComponent($form);
        $licensePlateName = $rdwApiLicenseplate->getStatePath(false);

        // Set data for RDW request
        $this->licenseplate = $data[$licensePlateName];
        $this->outputformat = $rdwApiLicenseplate->getOutputFormat();
        $this->language = $rdwApiLicenseplate->getLanguage();

        return $this->setEndpoints($rdwApiLicenseplate->getDataset());
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Get filament component
     *
     * @throws RdwException
     */
    final private function getComponent(Form $form): RdwApiLicenseplate
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
