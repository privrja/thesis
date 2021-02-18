<?php

namespace App\CycloBranch;

use App\Base\ReferenceHelper;
use App\Entity\Block;

class BlockCycloBranch extends AbstractCycloBranch {

    /**
     * @see AbstractCycloBranch::download()
     */
    public function download(): string {
        $this->data = '';
        /** @var Block[] $arResult */
        $arResult = $this->repository->findBy(['container' => $this->containerId]);
        if (!empty($arResult)) {
            foreach ($arResult as $block) {
                $this->data .= $block->getBlockName() . "\t"
                    . $block->getAcronym() . "\t"
                    . $block->getResidue() . "\t"
                    . $block->getBlockMass() . "\t"
                    . $block->getLosses() . "\t"
                    . ReferenceHelper::reference($block->getSource(), $block->getIdentifier(), $block->getUsmiles())
                    . PHP_EOL;
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
