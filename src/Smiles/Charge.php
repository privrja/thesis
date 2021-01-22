<?php

namespace App\Smiles;

use InvalidArgumentException;

class Charge {

    /** @var string sign of charge '+' | '-' | '' */
    private $sign;

    /** @var int size of charge */
    private $chargeSize;

    /**
     * Charge constructor.
     * @param string $sign
     * @param int $charge charge size positive int
     */
    public function __construct(string $sign = "", int $charge = 0) {
        if (($sign !== '+' && $sign !== '-' && !empty($sign)) || $charge < 0) {
            throw new InvalidArgumentException();
        }
        $this->sign = $sign;
        $this->chargeSize = $charge;
    }

    /**
     * For non negative return 0
     * for negative return 1
     * @return int
     */
    public function getSignValue() {
        return $this->sign === '-' ? 1 : 0;
    }

    /**
     * @return string
     */
    public function getSign(): string {
        return $this->sign;
    }

    /**
     * @return int
     */
    public function getChargeSize(): int {
        return $this->chargeSize;
    }

    public function isZero() {
        return $this->chargeSize === 0;
    }

    public function getCharge() {
        if ($this->chargeSize === 0) {
            return "";
        }
        return $this->sign . $this->chargeSize;
    }

}
