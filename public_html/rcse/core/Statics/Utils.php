<?php
declare(strict_types=1);

namespace RCSE\Core\Statics;

use DateTime;
use Exception;

class Utils
{
    public static array $knownOSs = [
        'Windows' => 'Windows OS',
        'Mac OS' => 'Max OS X',
        'Macintosh' => 'Mac OS X',
        'iPhone' => 'iOS',
        'iPod' => 'iOS',
        'iPad' => 'iOS',
        'FreeBSD' => 'FreeBSD',
        'OpenBSD' => 'OpenBSD',
        'NetBSD' => 'NetBSD',
        'DragonFly' => 'DragonFlyBSD',
        'SunOS' => 'SunOS/Solaris',
        'Gentoo' => 'Gentoo Linux',
        'Mint' => 'Mint Linux',
        'CentOS' => 'CentOS Linux',
        'Fedora' => 'Fedora Linux',
        'RedHat' => 'RedHat Linux',
        'SUSE' => 'SUSE Linux',
        'Arch' => 'Arch Linux',
        'Slackware' => 'Slackware Linux',
        'Kubuntu' => 'Kubuntu Linux',
        'Ubuntu' => 'Ubuntu Linux',
        'Debian' => 'Debian Linux',
        'Android' => 'Android OS',
        'Linux' => 'Linux OS',
        'CP/M' => 'CP/M OS',
        'AmigaOS' => 'Amiga OS',
        'CrOS' => 'Chrome OS',
        'Syllable' => 'Syllable OS',
        'Haiku' => 'Haiku/BeOS',
        'BeOS' => 'BeOS',
        'Series 60' => 'Symbian S60 OS',
        'BlackBerry' => 'BlackBerry OS',
        'J2ME/MIDP' => 'Plain Cell Phone'
    ];
    public static array $knownBrowsers = [
        'Opera' => 'Opera Browser',

        'Chimera' => 'Chimera Browser',
        'Lynx' => 'Lynx Browser',
        'Konqueror' => 'Konqueror Browser',
        'Kazehakase' => 'Kazehakase Browser',
        'K-Meleon' => 'K-Meleon Browser',
        'Galeon' => 'Galeon Browser',
        'EnigmaFox' => 'Enigma Browser',
        'Enigma Browser' => 'Enigma Browser',
        'Elinks' => 'ELinks Browser',
        'Dooble' => 'Dooble Browser',
        'Dillo' => 'Dillo Browser',
        'DeskBrowse' => 'DeskBrowse Browser',
        'Conkeror' => 'Conkeror Browser',
        'Charon' => 'Charon Browser',
        'AmigaVoyager' => 'Amiga Voyager Browser',

        'AcooBrowser' => 'Acoo Browser',
        'America Online' => 'America Online Browser',
        'AOL' => 'America Online Browser',
        'AvantBrowser' => 'Avant Browser',
        'Browzar' => 'Browzar Browser',
        'Crazy Browser' => ' Crazy Browser',
        'Deepnet Explorer' => 'Deepnet Explorer',
        'Green Browser' => 'Green Browser',
        'Lunascape' => 'Lunascape Browser',
        'MSIE' => 'Internet Explorer (or compatible)',

        'Iron' => 'Iron Browser',
        'Comodo_Dragon' => 'Comodo Dragon Browser',
        'Yowser' => 'Yandex Browser',
        'YaBrowser' => 'Yandex Browser',
        'ChromePlus' => 'ChromePlus Browser',
        'Chrome' => 'Chrome Browser (or compatible)',

        'Midori' => 'Midori Browser',
        'Epiphany' => 'Epiphany Browser',
        'Classilla' => 'Classilla Browser',
        'Camino' => 'Camino Browser',
        'Beonex' => 'Beonex Browser',
        'Iceweasel' => 'IceWeasel Browser',
        'IceCat' => 'IceWeasel Browser',
        'Iceape' => 'IceWeasel Browser',
        'BonEcho' => 'Firefox Browser (or compatible)',
        'GranParadiso' => 'Firefox Browser (or compatible)',
        'Namoroka' => 'Firefox Browser (or compatible)',
        'Firefox' => 'Firefox Browser (or compatible)',

        'iCab' => 'iCab Browser',
        'Fluid' => 'Fluid Browser',
        'Arora' => 'Arora Browser',
        'Links' => 'Links Browser',
        'Safari' => 'Safari (or compatible)',
    ];

    /**
     * Returns client's IP address
     *
     * @return string
     */
    public static function getClientIP() : string
    {
        if (!empty(GlobalArrays::getServerArrayEntry('HTTP_CLIENT_IP'))) {
            $ip = GlobalArrays::getServerArrayEntry('HTTP_CLIENT_IP');
        } elseif (!empty(GlobalArrays::getServerArrayEntry('HTTP_X_FORWARDED_FOR'))) {
            $ip = GlobalArrays::getServerArrayEntry('HTTP_X_FORWARDED_FOR');
        } else {
            $ip = GlobalArrays::getServerArrayEntry('REMOTE_ADDR');
        }

        return $ip;
    }

    /**
     * Returns current timestamp
     *
     * @param string $format
     * @param string $interval
     * @return string
     */
    public static function getTimestamp(string $format = 'Y-m-d H:i:s', string $interval = 'now') : string
    {
        return date($format, strtotime($interval));
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

    /**
     * Generates random key of given length using random_bytes
     *
     * @param int $length
     * @return string
     * @throws Exception
     */
    public static function generateKey(int $length) : string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * @return string
     */
    public static function getClientBrowser() : string
    {
        $user_agent = GlobalArrays::getServerArrayEntry('HTTP_USER_AGENT');
        foreach (self::$knownBrowsers as $key => $val)
        {
            if (strpos($user_agent, $key)) return $val;
        }

        return 'Other browser';
    }

    /**
     * @return string
     */
    public static function getClientOS() : string
    {
        $user_agent = GlobalArrays::getServerArrayEntry('HTTP_USER_AGENT');
        foreach (self::$knownOSs as $key => $val)
        {
            if (strpos($user_agent, $key)) return $val;
        }

        return 'Other OS';
    }
}