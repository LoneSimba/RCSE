<?php

namespace App\Contracts\Models;

use App\ParameterObjects\Source;

interface Parameterizable
{
    public function parameterize(): Source;

    public static function sourceType(): string;
}
