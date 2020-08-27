<?php

namespace App\Structure;

class NewContainerTransformed extends AbstractTransformed {

    private $containerName;
    private $visibility;

    /**
     * @return mixed
     */
    public function getContainerName() {
        return $this->containerName;
    }

    /**
     * @param mixed $containerName
     */
    public function setContainerName($containerName): void {
        $this->containerName = $containerName;
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