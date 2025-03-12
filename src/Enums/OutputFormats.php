<?php

namespace Jdkweb\RdwApi\Filament\Enums;

use Jdkweb\RdwApi\Enums\OutputFormats as BaseEnum;
use Filament\Support\Contracts\HasLabel;
use Jdkweb\RdwApi\Enums\Interface\OutputFormat;


enum OutputFormats: string implements HasLabel, OutputFormat
{
    use \Jdkweb\RdwApi\Enums\Traits\OutputFormats;

    case ARRAY = BaseEnum::ARRAY->value;
    case JSON = BaseEnum::JSON->value;
    case XML = BaseEnum::XML->value;
}
