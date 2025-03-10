<?php

namespace Jdkweb\RdwApi\Filament\Enums;

use Jdkweb\RdwApi\Enums\OutputFormat as BaseEnum;
use Filament\Support\Contracts\HasLabel;

enum OutputFormat: string implements HasLabel
{
    case ARRAY = BaseEnum::ARRAY->value;
    case JSON = BaseEnum::JSON->value;
    case XML = BaseEnum::XML->value;
}
