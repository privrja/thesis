<?php

namespace App\CycloBranch;

class BlockMergeFormulaCycloBranch extends BlockCycloBranch {

    /**
     * @see AbstractCycloBranch::download()
     */
    public function download(): string {
        $this->data = '';
        /** @var Object[] $arResult */
        $arResult = $this->repository->findMergeByFormula($this->containerId);
        if (!empty($arResult)) {
            foreach ($arResult as $block) {
                $this->data .= $block['block_name'] . "\t"
                    . $block['acronym'] . "\t"
                    . $block['residue'] . "\t"
                    . $block['block_mass'] . "\t"
                    . $block['losses'] . "\t"
                    . $block['ref']
                    . PHP_EOL;
            }
        }
        return $this->data;
    }

}
