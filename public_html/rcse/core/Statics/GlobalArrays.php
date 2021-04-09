<?php


namespace RCSE\Core\Statics;


class GlobalArrays
{
    public static function getPostArrayEntry(string $key)
    {
        return (isset($_POST[$key]) ? htmlspecialchars(trim($_POST[$key])) : null);
    }

    public static function getPostArray(): array
    {
        $arr = [];
        foreach($_POST as $key => $val)
        {
             $arr[$key] = htmlspecialchars(trim($val));
        }

        return $arr;
    }

    public static function getCookiesArrayEntry(string $key)
    {
        return (isset($_COOKIE[$key]) ? htmlspecialchars(trim($_COOKIE[$key])) : null);
    }

    public static function getCookiesArray(): array
    {
        $arr = [];
        foreach($_COOKIE as $key => $val)
        {
            $arr[$key] = htmlspecialchars(trim($val));
        }

        return $arr;
    }

    public static function getGetArrayEntry(string $key)
    {
        return (isset($_GET[$key]) ? htmlspecialchars(trim($_GET[$key])) : null);
    }

    public static function getGetArray() : array
    {
        $arr = [];

        foreach($_GET as $key => $val)
        {
            $arr[$key] = htmlspecialchars(trim($val));
        }

        return $arr;
    }

    public static function getServerArrayEntry(string $key)
    {
        return (isset($_SERVER[$key]) ? $_SERVER[$key] : null);
    }

    public static function getServerArray() : array
    {
        return $_SERVER;
    }
}