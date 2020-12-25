<?php


namespace RCSE\Core;


class ServerArray
{
    public static function get($key)
    {
        return (isset($_SERVER[$key]) ? $_SERVER[$key] : null);
    }

}