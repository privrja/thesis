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
                $this->data .= str_replace(',', '.', $block['block_name']) . self::TABULATOR
                    . $block['acronym'] . self::TABULATOR
                    . $block['residue'] . self::TABULATOR
                    . $block['block_mass'] . self::TABULATOR
                    . $block['losses'] . self::TABULATOR
                    . $block['ref']
                    . PHP_EOL;
            }
        }
        return $this->data;
    }

}
