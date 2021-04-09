<?php

namespace App\Base;

use Exception;

class GeneratorHelper {

    static function generate(int $length) {
        try {
            return bin2hex(random_bytes($length));
        } catch (Exception $e) {
            return rand(1000000, 999999999999);
        }
    }

}
