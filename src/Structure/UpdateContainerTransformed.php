<?php


namespace App\Controller;


use App\Structure\AbstractTransformed;

class UpdateContainerTransformed extends AbstractTransformed {

    private $containerId;
    private $name;
    private $visibility;

    /**
     * @return mixed
     */
    public function getContainerId() {
        return $this->containerId;
    }

    /**
     * @param mixed $containerId
     */
    public function setContainerId($containerId): void {
        $this->containerId = $containerId;
    }

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
