<?php
declare(strict_types=1);

namespace RCSE\Core;

class Utils
{
    public static function transformType($data)
    {
        $res = '';
        switch (gettype($data)) {
            case 'integer':
            case 'double':
                $res .= $data;
                break;
            case 'string':
                $res .= "'{$data}'";
                break;
            case 'boolean':
                $res .= (int) $data;
                break;
            }
            
        return $res;
    }

}