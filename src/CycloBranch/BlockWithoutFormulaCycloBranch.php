<?php

namespace App\CycloBranch;

class BlockWithoutFormulaCycloBranch extends BlockCycloBranch {

    /**
     * @see AbstractCycloBranch::download()
     */
    public function download() {
//        $start = 0;
//        $arResult = $this->database->findAllPaging($start, new Query());
//        while (!empty($arResult)) {
//            foreach ($arResult as $modification) {
//                $strData = $modification[BlockTO::NAME] . "\t";
//                $strData .= $modification[BlockTO::ACRONYM] . "\t";
//                $strData .= $modification[BlockTO::RESIDUE] . "\t";
//                $strData .= $modification[BlockTO::MASS] . "\t";
//                $strData .= $modification[BlockTO::LOSSES] . "\t";
//                $strData .= ReferenceHelper::reference($modification['database'], $modification['identifier'], $modification['smiles']);
//                $strData .= PHP_EOL;
//                file_put_contents(self::FILE_NAME, $strData, FILE_APPEND);
//            }
//            $start += CommonConstants::PAGING;
//            $arResult = $this->database->findAllPaging($start, new Query());
//        }
    }

}
