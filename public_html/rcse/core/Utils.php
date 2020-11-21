<?php
declare(strict_types=1);

namespace RCSE\Core;

use DateTime;
use Exception;

class Utils
{
    /**
     *
     * @param mixed $data
     * @return string
     * @todo Consider removing this
     */
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

    /**
     * Returns client's IP address
     *
     * @return string
     */
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

    /**
     * Returns current timestamp
     *
     * @param bool $formatted
     * @return DateTime|string
     * @throws Exception
     */
    public static function getTimestamp(bool $formatted = true)
    {
        $stamp = date('Y-m-d H:i:s');
        $date = new DateTime($stamp);
        
        if($formatted) {
            return $date->format('Y-m-d H:i:s');
        } else {
            return $date;
        }
    }

    /**
     * Generates UUID v4 using provided $data or random_bytes
     *
     * @param string|null $data
     * @return string
     * @throws Exception
     */
    public static function generateUUID(string $data = null) : string
    {
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}