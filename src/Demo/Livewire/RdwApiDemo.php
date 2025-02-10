<?php

namespace Jdkweb\Rdw\Filament\Demo\Livewire;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Jdkweb\Rdw\Controllers\RdwApiResponse;
//use Jdkweb\Rdw\Filament\Controllers\RdwApiRequest;
use Jdkweb\Rdw\Enums\OutputFormat;
use Jdkweb\Rdw\Filament\Controllers\RdwApiRequest;
use Livewire\Component;
use Illuminate\Contracts\View\View;

class RdwApiDemo extends Component implements HasForms
{
    use InteractsWithForms;

    /**
     * Filament example forms
     */
    use RdwApiExampleForm1, RdwApiExampleForm2, RdwApiExampleForm3;

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
    protected OutputFormat $output_format;
    protected string $language;

    protected $layout = 'rdw_views::components.layouts.rdw-api-demo';

    //------------------------------------------------------------------------------------------------------------------

    public function __construct()
    {
        $this->setLanguage();

        // views from rdw-api adn rdw-api-filament
        view()->addNamespace('rdw_views', [
                dirname(dirname(dirname(dirname(__DIR__)))) . '/rdw-api/src/Demo/views/',
                dirname(__DIR__) . '/views/'
            ]);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Set language with part of the uri
     *
     * @return void
     */
    protected function setLanguage():void
    {
        $url = config('rdw-api.rdw_api_folder') . "/".
            config('rdw-api.rdw_api_filament_folder') . "/".
            config('rdw-api.rdw_api_demo_slug');

        $language = app()->getLocale();
        if (preg_match("/^" . addcslashes($url, "/") . "\/(nl|en)$/", request()->path())) {
            $language = str_replace($url."/", "", request()->path());
        }
        app()->setLocale($language);
    }

    //------------------------------------------------------------------------------------------------------------------

    public function mount():void
    {
        $this->exampleForm1->fill();
        $this->exampleForm2->fill();
        $this->exampleForm3->fill();
    }

    //------------------------------------------------------------------------------------------------------------------

    protected function getForms(): array
    {
        return [
            'exampleForm1',
            'exampleForm2',
            'exampleForm3',
        ];
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Handle submitted Filament form
     *
     * @param  string  $form
     * @return void
     */
    public function handleForm(string $form): void
    {

        $result = RdwApiRequest::make()
            ->setFormData($this->{$form})
            ->fetch();

        // check method
        if (preg_match("/^exampleForm[0-9]{1}$/", $form) &&
           method_exists($this, $form)) {
             $this->{$form."Handler"}($result);
        }

        // create Data output
        $this->livewireOutput($result);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Create output for livewire form
     *
     * @param  RdwApiResponse  $response
     * @return void
     */
    protected function livewireOutput(RdwApiResponse $data): void
    {
        switch ($data->request->outputformat) {
            case OutputFormat::XML:
                $this->livewire_results = view(
                    'rdw_views::components.xml',
                    ['results' => $data->toXml(true)]
                );
                break;
            case OutputFormat::JSON:
                $this->livewire_results = view(
                    'rdw_views::components.json',
                    ['noscript' => true]
                );
                $this->dispatch('getJsonResult', result: $data->toJson());
                break;
            default:
                $this->livewire_results = view(
                    'rdw_views::components.array',
                    ['results' => $data->toArray()]
                );
        };
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Clear by tab switch
     *
     * @return void
     */
    public function clear(): void
    {
        $this->results = null;
        $this->livewire_results = null;
    }

    //------------------------------------------------------------------------------------------------------------------

    public function render(): View
    {
        // Pass not livewire data for the post form
        return view('rdw_views::livewire.rdw-api-demo')
            ->layout($this->layout, [
                'language' => app()->getLocale(),
            ]);
    }
}
