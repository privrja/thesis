<?php

namespace App\Structure;

class NewContainerTransformed extends AbstractTransformed {

    private $name;
    private $visibility;

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
    public function getVisibility() {
        return $this->visibility;
    }

    /**
     * @param mixed $visibility
     */
    public function setVisibility($visibility): void {
        $this->visibility = $visibility;
    }

}