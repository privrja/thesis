<?php

namespace App\Constant;

class Constants {

    /** @var string ENDPOINT address */
    public const ENDPOINT = 'https://localhost:8000/rest/';

    /** @var int Logout time in seconds */
    public const LOGOUT_TIME = 3600;

    public static function getLocation(string $uri, int $id) {
        return ['Location' => self::ENDPOINT . $uri . $id];
    }

}
