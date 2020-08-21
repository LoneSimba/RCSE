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

    public static function getClientIP() : string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    public static function getTimestamp(bool $formatted = true)
    {
        $stamp = date('Y-m-d H:i:s');
        $date = new \DateTime($stamp);
        
        if($formatted) {
            return $date->format('Y-m-d H:i:s');
        } else {
            return $date;
        }
    }
}