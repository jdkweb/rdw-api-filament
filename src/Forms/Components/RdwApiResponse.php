<?php
namespace Jdkweb\Rdw\Filament\Forms\Components;

use Jdkweb\Rdw\Enums\Endpoints;
use Jdkweb\Rdw\Enums\OutputFormat;

class RdwApiResponse
{
    public ?string $state = null;
    public ?array $endpoints = null;
    public ?string $language = null;
    public ?string $format = null;
    public ?array $response = null;
    public bool $ok = false;


    public function __call(string $name, array $arguments): array|string|null
    {
        return $this->getResponse($name);
    }

    public function __get(string $name): array|string|null
    {
        return $this->getResponse($name);
    }

    /**
     * Get response data, depends on selected language
     * fieldname always the dutch name
     *
     * @param  Endpoints  $dataSet
     * @param  string  $fieldName
     * @return string|null
     */
    public function get(Endpoints $dataSet, string $fieldName):?string
    {
        $lang_dataSet = strtolower($dataSet->name);

        return $this->response[$dataSet->getName()][__("rdw-api::".$lang_dataSet.".".$fieldName)] ?? '';
    }

    protected function getResponse(string $key): ?array
    {
        if(is_null($this->response)) return null;

        if(!is_null(Endpoints::getCase($key)) &&
            Endpoints::getCase($key)::class == "Jdkweb\Rdw\Enums\Endpoints") {
            $key = Endpoints::getCase($key)->getName();
            if(array_key_exists($key, $this->response)) {
                $this->ok = true;
                return $this->response[$key];
            }
        }

        return null;
    }
}
