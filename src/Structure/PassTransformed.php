<?php

namespace App\Structure;

class PassTransformed extends AbstractTransformed {

    /** @var string */
    private $password;

    /**
     * @return string
     */
    public function getPassword(): string {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void {
        $this->password = $password;
    }

}
