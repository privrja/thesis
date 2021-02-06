<?php

namespace App\Structure;

class FamilyTransformed extends AbstractTransformed {

    /** @var string */
    private $family;

    /**
     * @return string
     */
    public function getFamily(): string {
        return $this->family;
    }

    /**
     * @param string $family
     */
    public function setFamily(string $family): void {
        $this->family = $family;
    }

}
