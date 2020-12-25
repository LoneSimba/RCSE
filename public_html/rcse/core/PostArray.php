<?php


namespace RCSE\Core;


class PostArray
{
    public static function get($key)
    {
        return (isset($_POST[$key]) ? $_POST[$key] : null);
    }
}