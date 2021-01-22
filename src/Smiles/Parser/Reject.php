<?php

namespace App\Smiles\Parser;

use App\Exception\IllegalStateException;

class Reject extends ParseResult {

    /**
     * Reject constructor.
     * @param string $errorMessage
     */
    public function __construct($errorMessage) {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return boolean
     */
    public function isAccepted() {
        return false;
    }

    /**
     * @return mixed
     * @throws IllegalStateException
     */
    public function getResult() {
        throw new IllegalStateException();
    }

    /**
     * @return string
     */
    public function getErrorMessage() {
        return $this->errorMessage;
    }

    /**
     * @return string
     * @throws IllegalStateException
     */
    public function getRemainder() {
        throw new IllegalStateException();
    }

}
