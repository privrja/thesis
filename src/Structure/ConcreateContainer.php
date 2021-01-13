<?php

namespace App\Structure;

use App\Constant\EntityColumnsEnum;
use JsonSerializable;

class ConcreateContainer implements JsonSerializable {

    private $userId;
    private $containerName;
    private $visibility;
    private $collaborators;

    /**
     * ConcreateContainer constructor.
     * @param $userId
     * @param $containerName
     * @param $visibility
     * @param $collaborators
     */
    public function __construct($userId, $containerName, $visibility, $collaborators) {
        $this->userId = $userId;
        $this->containerName = $containerName;
        $this->visibility = $visibility;
        $this->collaborators = $collaborators;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize() {
        return [
            EntityColumnsEnum::ID => $this->userId,
            EntityColumnsEnum::CONTAINER_NAME => $this->containerName,
            EntityColumnsEnum::CONTAINER_VISIBILITY => $this->visibility,
            EntityColumnsEnum::COLLABORATORS => $this->collaborators
        ];
    }

}
