<?php

namespace App\CycloBranch;

use App\Base\ReferenceHelper;
use App\Entity\Container;
use App\Entity\Sequence;
use Doctrine\ORM\EntityManagerInterface;

class SequenceCycloBranch extends AbstractCycloBranch {

    /**
     * @see AbstractCycloBranch::download()
     */
    public function download(): string {
        $this->data = '';
        /** @var Sequence[] $arResult */
        $arResult = $this->repository->findBy(['container' => $this->containerId]);
        if (!empty($arResult)) {
            foreach ($arResult as $sequence) {
                $this->data .= $sequence->getSequenceType() . self::TABULATOR
                    . $sequence->getSequenceName() . self::TABULATOR
                    . $sequence->getSequenceFormula() . self::TABULATOR
                    . $sequence->getSequenceMass() . self::TABULATOR
                    . $sequence->getSequence() . self::TABULATOR
                    . (($sequence->getNModification() !== null) ? $sequence->getNModification()->getModificationName() : '') . self::TABULATOR
                    . (($sequence->getCModification() !== null) ? $sequence->getCModification()->getModificationName() : '') . self::TABULATOR
                    . (($sequence->getBModification() !== null) ? $sequence->getBModification()->getModificationName() : '') . self::TABULATOR
                    . ReferenceHelper::reference($sequence->getSource(), $sequence->getIdentifier(), $sequence->getSequenceSmiles())
                    . PHP_EOL;
            }
        }
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function import(Container $container, EntityManagerInterface $entityManager, array $okStack, array $errorStack): array {
        // TODO: Implement import() method.
    }
}
