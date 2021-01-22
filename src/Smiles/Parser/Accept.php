<?php

namespace App\Smiles\Parser;

use App\Exception\IllegalStateException;

class Accept extends ParseResult {

    /**
     * Accept constructor.
     * @param $result
     * @param string $remainder
     */
    public function __construct($result, $remainder) {
        $this->result = $result;
        $this->remainder = $remainder;
    }

    /**
     * @return boolean
     */
    public function isAccepted() {
        return true;
    }

    /**
     * @return mixed
     */
    public function getResult() {
        return $this->result;
    }

    /**
     * @return string
     * @throws IllegalStateException
     */
    public function getErrorMessage() {
        throw new IllegalStateException();
    }

    /**
     * @return string
     */
    public function getRemainder() {
        return $this->remainder;
    }

}
