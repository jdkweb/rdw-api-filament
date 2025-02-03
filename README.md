# RDW API extension for Filament
Laravel application to get vehicle information from [opendata.rdw.nl](https://opendata.rdw.nl) or [overheid.io](https://overheid.io). \
Depends on [jdkweb/rdw-api](https://github.com/jdkweb/rdw-api?tab=readme-ov-file#demo). \
This package extends [jdkweb/rdw-api](https://github.com/jdkweb/rdw-api?tab=readme-ov-file#demo) to be used in [Filament](https://filamentphp.com/)

## Table of contents

- [Installation](#installation)
- [Translation](#translation)
- [Usage](#usage)
- [Demo](#demo)
- [Extension for Filament](#filament)

## Installation
Requires PHP 8.2 and Laravel 10 and Filament 3 or higher \
Install the package via composer:
```bash
composer require jdkweb/rdw-api-filament
```
## Translation
[See jdkweb/rdw-api](https://github.com/jdkweb/rdw-api?tab=readme-ov-file#translation)

## Usage
![filament setup](./images/rdw-api-filament1.png)
```php
use Jdkweb\Rdw\Enums\Endpoints;
use Jdkweb\Rdw\Enums\OutputFormat;
use Jdkweb\Rdw\Filament\Forms\Components\RdwApiLicenseplate;
use Jdkweb\Rdw\Filament\Forms\Components\RdwApiResponse;
use Jdkweb\Rdw\Filament\Forms\Components\RdwApiSelectDataset;

RdwApiSelectDataset::make('datasets')
    ->label(__('rdw-api::form.selectdatasetLabel'))
    ->dataSet('all')
    ->setDefault(fn() => ['vehicle'])
    ->shortname(true)
    ->showSelectAll()
    ->multiple()
    ->required(),
    
RdwApiLicenseplate::make('licenseplate')
    ->label(__('rdw-api::form.licenseplateLabel'))
    ->default('155-GV-3')
    ->licenseplateStyle()
    ->required(),
    
Forms\Components\Select::make('output_format')
    ->label(__('rdw-api::form.formatLabel'))
    ->required()
    ->options(OutputFormat::getOptions())
```
### RdwApiLicenseplate Options
#### Preset for Dutch licenseplate style
```php
->licenseplateStyle()
```
#### Endpoints selected
When the options field RdwApiSelectDataset is not used 
```php
->dataSet(['vehicle','fuel'])
```
Options for endpoints (not case sensitive):
- vehicle
- vehicle_class
- fuel
- bodywork
- bodywork_specific
- axles 
- all
- 
#### Format of the response output
```php  
->outputFormat('array')
```
### RdwApiSelectDataset Options
#### Available endpoints to select
```php
->dataSet('all') # show all datasets
->dataSet(['vehicle','fuel']) # show only vehicle and fuel
```
Options for endpoints (not case sensitive):
- vehicle
- vehicle_class
- fuel
- bodywork
- bodywork_specific
- axles 
- all
#### Preset selection
```php
->setDefault(['vehicle'])
->setDefault(fn() => (request()->ip() == '192.168.1.1' ? 'vehicle' : 'all'))
```
#### Use shortnames for RDW datasets
```php
->shortname(true) // 'Vehicles' instead of 'Registered vehicles'
```
#### Show select all link
```php
->showSelectAll()
```
