<?php

namespace App\CycloBranch;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class CycloBranch {

    /** @var ServiceEntityRepository */
    private $repository;

    /** @var int */
    private $type;

    /** @var int */
    private $containerId;

    /**
     * CycloBranch constructor.
     * @param int $type
     * @param ServiceEntityRepository $repository
     * @param int $containerId
     */
    public function __construct(int $type, ServiceEntityRepository $repository, int $containerId) {
        $this->type = $type;
        $this->repository = $repository;
        $this->containerId = $containerId;
    }

    /**
     * Get right Import class and import it
     * @param string $filePath path to uploaded file
     */
    public function import(string $filePath) {
        $cycloBranch = ImportTypeFactory::getCycloBranch($this->type, $this->repository, $this->containerId);
        $cycloBranch->import($filePath);
    }

}
