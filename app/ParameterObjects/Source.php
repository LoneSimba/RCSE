<?php

namespace App\ParameterObjects;

/**
 * @property string $id
 * @property string $type
 */
class Source extends BaseStruct
{
    protected $id;

    protected $type;

    public function __construct(string $id, string $type)
    {
        $this->id = $id;
        $this->type = $type;
    }
}
