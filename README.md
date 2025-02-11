# RDW API extension for Filament
Laravel application to get vehicle information from [opendata.rdw.nl](https://opendata.rdw.nl) or [overheid.io](https://overheid.io). \
Depends on [jdkweb/rdw-api](https://github.com/jdkweb/rdw-api?tab=readme-ov-file#demo). \
This package extends [jdkweb/rdw-api](https://github.com/jdkweb/rdw-api?tab=readme-ov-file#demo) to be used in [Filament](https://filamentphp.com/)

## Table of contents

- [Installation](#installation)
- [Translation](#translation)
- [Usage](#usage)
  - [Form Field](#formfield)
  - [Handle Response](#response)
- [Example](#example)  
- [Demo](#demo)


## Installation
Requires PHP 8.2 and Laravel 10 and Filament 3 or higher \
Install the package via composer:
```bash
composer require jdkweb/rdw-api-filament
```
## Translation
[See jdkweb/rdw-api](https://github.com/jdkweb/rdw-api/tree/main?tab=readme-ov-file#translation)

# Usage
- [Filament Form Field: RdwApiRequest](#formfield)
- [Response RdwApiResponse](#response)
 
## <a name="formfield"></a>Form Field
### Basic usage
![filament setup](./images/rdw-api-filament1.webp)
```php
use Jdkweb\Rdw\Filament\Forms\Components\RdwApiLicenseplate;
...
RdwApiLicenseplate::make('licenseplate')
    ->label(__('rdw-api::form.licenseplateLabel'))
    ->default('155GV3')    
    ->licenseplateStyle()  // Basic style Dutch licenseplate
```
- Request to the active API (default: opendata.rdw.nl) \
- All RDW endpoints are selected
### All options used
```php
use Jdkweb\Rdw\Enums\Endpoints;
use Jdkweb\Rdw\Enums\OutputFormat;
use Jdkweb\Rdw\Filament\Forms\Components\RdwApiLicenseplate;
...
RdwApiLicenseplate::make('licenseplate')
    ->label(__('rdw-api::form.licenseplateLabel'))
    ->default('155GV3')    
    ->setApi(1)
    ->setEndpoints(Endpoints::cases())    
    ->setOutputformat(OutputFormat::ARRAY)    
    ->setLanguage('en')
    ->licenseplateStyle() 
```
### Options
#### Select other API than default
```php
->setApi(int|string|\Closure) // 0 | opendata | 1 | overheid    
```
Overwrite the config settings 
- 0 or 'opendata' for using the RDW API opendata.rdw.nl **[default]**
- 1 or 'overheidio' for using the overheid.io API

#### Select endpoints for request 
```php
use \Jdkweb\Rdw\Enums\Endpoints;
...
->setEndpoints(string|array|Endpoints|\Closure)

#examples  
    # one string
    ->setEndpoints('vehicle')       
    # array with strings
    ->setEndpoints([                
        'vehicle',
        'fuel'
    ])
    # array with endpoints
    ->setEndpoints([                
        Endpoints::VEHICLE,
        Endpoints::FUEL,    
    ])    
    # closure
    ->setEndpoints(fn() => ($when ? Endpoints::cases() : Endpoints::BODYWORK))
    # select all
    ->setEndpoints(Endpoints::cases())    
````
Available endpoints (not case sensitive):
- Endpoints::VEHICLE | vehicle
- Endpoints::VEHICLE_CLASS |vehicle_class
- Endpoints::FUEL | fuel
- Endpoints::BODYWORK | bodywork
- Endpoints::BODYWORK_SPECIFIC | bodywork_specific
- Endpoints::AXLES | axles 
- Endpoints::cases() **[default]**

#### Format of the response output
```php  
->outputFormat(string|OutputFormat|\Closure)

#examples  
    ->outputFormat('array')
    ->outputFormat(OutputFormat::ARRAY)
    ->setOutputformat(fn(Forms\Get $get) => $get('output_format'))
```
- OutputFormat::ARRAY | array **[default]**
- OutputFormat::JSON | json
- OutputFormat::AML | xml

by using this method the response contains a formated output. see [RdwApiResponse](#RdwApiResponse)  

#### Set output language
```php
->setLanguage(string|\Closure)
```
Force output language, so form can be English and RDW response in Dutch. \
Available:
  - nl 
  - en

#### Basic style for Dutch licenseplate
```php
->licenseplateStyle() 
->licenseplateStyle('taxi')  // blue taxi plate 
```

### <a title="response"></a>Handle response
```php
public function handleForm(string $form): void
{
    $result = RdwApiRequest::make()
        ->setFormData($this->form)
        ->fetch();
```
#### Response
```php
Jdkweb\Rdw\Controllers\RdwApiResponse {#2800 ▼
  +response: array:2 [▶]    // API response
  +request: {#3036 ▶}       // Request vars
  +output: array:2 [▶]      // Formated output when setOutputFormat is used
  +status: true
}
```
See [Response methods](https://github.com/jdkweb/rdw-api/tree/main?tab=readme-ov-file#response)

## Example
![filament setup](./images/rdw-api-filament2.webp)

Create Filament form
```php
use Jdkweb\Rdw\Enums\Endpoints;
use Jdkweb\Rdw\Enums\OutputFormat;
use Jdkweb\Rdw\Filament\Forms\Components\RdwApiLicenseplate;
use Jdkweb\Rdw\Filament\Forms\Components\RdwApiResponse;
use Jdkweb\Rdw\Filament\Controllers\RdwApiRequest;
...

/**
 * Dataset Selectbox 
 */
Forms\Components\Select::make('datasets')
    ->label(__('rdw-api::form.selectdatasetLabel'))
    ->multiple()
    ->options(Endpoints::class)
    ->default([
        Endpoints::VEHICLE,
        Endpoints::FUEL
    ])
    ->hintAction(selectAllDatasets())   // Helper function for select all link
    ->reactive() // Enables reactivity
    ->required(),

/**
 * Licenseplate
 *
 * Extra reactive data for Endpoints and outputFormat   
 */    
RdwApiLicenseplate::make('licenseplate')
    ->label(__('rdw-api::form.licenseplateLabel'))
    ->setOutputformat(fn(Forms\Get $get) => $get('output_format'))
    ->setEndpoints(fn(Forms\Get $get) => $get('datasets'))
    ->required()
    ->licenseplateStyle()
    ->live(true)
    ->afterStateUpdated(function ($state, Forms\Set $set) use ($form) {

        $result = \Jdkweb\Rdw\Filament\Controllers\RdwApiRequest::make()
            ->setFormData($form)
            ->fetch();

        if ($result->status === false) {
            return;
        }

        // Handle data
        // $set('merk', $result->quickSearch('merk'));
        // $set('voertuigsoort', $result->quickSearch('voertuigsoort'));
        // $set('brandstof_omschrijving', $result->quickSearch('1.brandstof_omschrijving')); // type or  hybrid: first type
        // $set('aslast', $result->quickSearch('2.wettelijk_toegestane_maximum_aslast')); // second axle       
        // ...    
    }),    

/**
 * Output format selectbox 
 */    
Forms\Components\Select::make('output_format')
    ->label(__('rdw-api::form.formatLabel'))
    ->required()
    ->default(OutputFormat::XML)
    ->options(OutputFormat::class)
    ->reactive() // Enables reactivity
```
Handle Form data
```php
...

public function handleForm(string $form): void
{

    // Get RDW data
    $result = RdwApiRequest::make()
        ->setFormData($this->form)
        ->fetch();
    ...
    ..
    
    // Handle data format 
    switch ($data->request->outputformat) {
        case OutputFormat::XML:
            $data->toXml(true)
    ...

```

## Demo
There is a demo available to test this wrapper \
Two options to use the demo:
1. ### .env
   ```php
    RDW_API_DEMO=1
   ```
   Add this value to .env
2. ### config
   Import the rwd-api config en set the value to 1 ([Installation](#installation))
   ```php
    rdw_api_demo => 1,
   ```
   Demo: 0 = Off | 1 = On

### Demo url
```html
http://[domainname]/rdw-api/demo
```
