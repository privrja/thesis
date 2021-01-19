<?php

namespace App\Controller;

class NewRegistrationStructure {

    /** @var string $name */
    private $name;

    /** @var string $password */
    private $password;

    /** @var string $mail */
    private $mail;

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPassword(): string {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getMail(): ?string {
        return $this->mail;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void {
        $this->name = $name;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void {
        $this->password = $password;
    }

    /**
     * @param string $mail
     */
    public function setMail(string $mail): void {
        $this->mail = $mail;
    }

}
