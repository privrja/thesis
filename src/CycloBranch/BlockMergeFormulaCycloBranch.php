<?php

namespace App\CycloBranch;

use Monolog\Logger;

class BlockMergeFormulaCycloBranch extends BlockCycloBranch {

    /**
     * @see AbstractCycloBranch::download()
     */
    public function download() {
        $this->data = '';
        /** @var Object[] $arResult */
        $arResult = $this->repository->findMergeByFormula($this->containerId);
        if (!empty($arResult)) {
            foreach ($arResult as $block) {
                $this->data .= $block->blockName() . "\t"
                    . $block->acronym() . "\t"
                    . $block->residue() . "\t"
                    . $block->blockMass() . "\t"
                    . $block->losses() . "\t"
                    . $block->ref
                    . PHP_EOL;
            }
        }
    }

}
