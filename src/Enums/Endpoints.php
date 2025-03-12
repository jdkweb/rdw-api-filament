<?php

namespace Jdkweb\RdwApi\Filament\Enums;

use Filament\Support\Contracts\HasLabel;
use Jdkweb\RdwApi\Enums\Endpoints as BaseEnum;
use Jdkweb\RdwApi\Enums\Interface\Endpoint;

enum Endpoints: string implements HasLabel, Endpoint
{
    use \Jdkweb\RdwApi\Enums\Traits\Endpoints;

    // Select option values
    case VEHICLE = BaseEnum::VEHICLE->value;
    case VEHICLE_CLASS = BaseEnum::VEHICLE_CLASS->value;
    case FUEL = BaseEnum::FUEL->value;
    case BODYWORK = BaseEnum::BODYWORK->value;
    case BODYWORK_SPECIFIC = BaseEnum::BODYWORK_SPECIFIC->value;
    case AXLES = BaseEnum::AXLES->value;
}
