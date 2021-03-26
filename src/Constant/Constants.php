<?php

namespace App\Constant;

class Constants {

    public const ENDPOINT = 'https://localhost:8000/rest/';

    public static function getLocation(string $uri, int $id) {
        return ['Location' => self::ENDPOINT . $uri . $id];
    }

}
