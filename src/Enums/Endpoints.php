<?php

namespace Jdkweb\RdwApi\Filament\Enums;

use Jdkweb\RdwApi\Enums\Endpoints as BaseEnum;

enum Endpoints: string implements HasLabel
{
    use EndpointsTrait;

    // Select option values
    case VEHICLE = BaseEnum::VEHICLE->value;
    case VEHICLE_CLASS = BaseEnum::VEHICLE_CLASS->value;
    case FUEL = BaseEnum::FUEL->value;
    case BODYWORK = BaseEnum::BODYWORK->value;
    case BODYWORK_SPECIFIC = BaseEnum::BODYWORK_SPECIFIC->value;
    case AXLES = BaseEnum::AXLES->value;
}
