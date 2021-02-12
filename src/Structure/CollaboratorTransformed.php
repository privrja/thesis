<?php

namespace App\Structure;

class CollaboratorTransformed extends AbstractTransformed {

    /** @var string */
    private $mode;

    /**
     * @return string
     */
    public function getMode(): string {
        return $this->mode;
    }

    /**
     * @param string $mode
     */
    public function setMode(string $mode): void {
        $this->mode = $mode;
    }

}
