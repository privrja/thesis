<?php

namespace App\CycloBranch;

use App\CycloBranch\Enum\ImportTypeEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class ImportTypeFactory {

    /**
     * Get right instance of AbstractCycloBranch for import
     * @param int $type
     * @param ServiceEntityRepository $repository
     * @param int $containerId
     * @return BlockCycloBranch|ModificationCycloBranch|SequenceCycloBranch
     * @see ImportTypeEnum
     */
    public static function getCycloBranch(int $type, ServiceEntityRepository $repository, int $containerId) {
        switch ($type) {
            case ImportTypeEnum::SEQUENCE:
                return new SequenceCycloBranch($repository, $containerId);
            default:
            case ImportTypeEnum::BLOCK:
                return new BlockCycloBranch($repository, $containerId);
            case ImportTypeEnum::MODIFICATION:
                return new ModificationCycloBranch($repository, $containerId);
        }
    }

}
