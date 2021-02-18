<?php

namespace App\CycloBranch;

use App\Entity\Modification;
use App\Smiles\Parser\Reject;

class ModificationCycloBranch extends AbstractCycloBranch {

    /**
     * @see AbstractCycloBranch::reject()
     */
    public static function reject() {
        return new Reject('Not match modification in right format');
    }

    /**
     * @see AbstractCycloBranch::download()
     */
    public function download(): string {
        $this->data = '';
        /** @var Modification[] $arResult */
        $arResult = $this->repository->findBy(['container' => $this->containerId]);
        if (!empty($arResult)) {
            foreach ($arResult as $modification) {
                $this->data .= $modification->getModificationName() . "\t"
                    . $modification->getModificationFormula() . "\t"
                    . $modification->getModificationMass() . "\t"
                    . ($modification->getNTerminal() ? '1' : '0') . "\t"
                    . ($modification->getCTerminal() ? '1' : '0') . PHP_EOL;
            }
        }
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function import() {
        // TODO: Implement import() method.
    }

}
