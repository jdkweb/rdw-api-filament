<?php

namespace Jdkweb\RdwApi\Enums;

use Filament\Support\Contracts\HasLabel;

enum OutputFormat: string implements HasLabel
{
    use OutputFormatTrait;

    case ARRAY = 'array';
    case JSON = 'json';
    case XML = 'xml';
}
