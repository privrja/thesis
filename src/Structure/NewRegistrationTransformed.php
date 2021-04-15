<?php

namespace App\Structure;

class NewRegistrationTransformed extends AbstractTransformed {

    public $name;
    public $password;
    public $mail;
    public $cap;

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getMail() {
        return $this->mail;
    }

    /**
     * @param mixed $mail
     */
    public function setMail($mail): void {
        $this->mail = $mail;
    }

}
