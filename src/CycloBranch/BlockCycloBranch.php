<?php

namespace App\CycloBranch;

use App\Exception\IllegalStateException;
use App\Smiles\Parser\Accept;
use App\Smiles\Parser\IParser;
use App\Smiles\Parser\ReferenceParser;
use App\Smiles\Parser\Reject;

class BlockCycloBranch extends AbstractCycloBranch {

    const NAME = 0;
    const ACRONYM = 1;
    const FORMULA = 2;
    const MASS = 3;
    const LOSSES = 4;
    const REFERENCE = 5;
    const LENGTH = 6;

    /**
     * @param string $line
     * @return Accept|Reject
     * @throws IllegalStateException
     * @see IParser::parse()
     * @see AbstractCycloBranch::parse()
     */
    public function parse(string $line) {
        $arItems = $this->validateLine($line, false);
        if ($arItems === false) {
            return self::reject();
        }

        $arNames = explode('/', $arItems[self::NAME]);
        $length = sizeof($arNames);
        $arSmiles = [];
        $arAcronyms = explode('/', $arItems[self::ACRONYM]);
        $arReference = explode('/', $arItems[self::REFERENCE]);
        $arDatabaseReference = [];
        if (sizeof($arAcronyms) !== $length || sizeof($arReference) !== $length) {
            return self::reject();
        }

        for ($index = 0; $index < $length; ++$index) {
            $referenceParser = new ReferenceParser();
            $referenceResult = $referenceParser->parse($arReference[$index]);
            if ($referenceResult->isAccepted()) {
                if ($referenceResult->getResult()->database === "SMILES") {
                    $arSmiles[$index] = $referenceResult->getResult()->identifier;
                    $arDatabaseReference[] = $referenceResult->getResult();
                } else {
                    $arSmiles[$index] = "";
                    $arDatabaseReference[] = $referenceResult->getResult();
                }
//                if ($arSmiles[$index] === "" && ($referenceResult->getResult()->database === ServerEnum::PUBCHEM || $referenceResult->getResult()->database === ServerEnum::CHEBI)) {
//                    $finder = FinderFactory::getFinder($referenceResult->getResult()->database);
//                    $findResult = null;
//                    $outArResult = [];
//                    try {
//                        $findResult = $finder->findByIdentifier($referenceResult->getResult()->identifier, $outArResult);
//                    } catch (BadTransferException $e) {
//                        Logger::log(LoggerEnum::WARNING, "Block not found");
//                    }
//                    if ($findResult === ResultEnum::REPLY_OK_ONE) {
//                        $arSmiles[$index] = $outArResult[Front::CANVAS_INPUT_SMILE];
//                    }
//                }
            } else {
                return self::reject();
            }
        }

        $arBlocks = [];
//        for ($index = 0; $index < $length; ++$index) {
//            $blockTO = new BlockTO(0, $arNames[$index], $arAcronyms[$index], $arSmiles[$index], ComputeEnum::UNIQUE_SMILES);
//            $blockTO->formula = $arItems[self::FORMULA];
//            $blockTO->mass = (float)$arItems[self::MASS];
//            $blockTO->losses = $arItems[self::LOSSES];
//            if ($arDatabaseReference[$index]->database !== "SMILES") {
//                $blockTO->database = $arDatabaseReference[$index]->database;
//                $blockTO->identifier = $arDatabaseReference[$index]->identifier;
//            }
//            $arBlocks[] = $blockTO->asEntity();
//        }
        return new Accept($arBlocks, '');
    }

    /**
     * @see AbstractCycloBranch::download()
     */
    public function download() {
//        $start = 0;
//        $arResult = $this->database->findMergeBlocks($start);
//        while (!empty($arResult)) {
//            foreach ($arResult as $formula) {
//                $strData = "";
//                $blockCount = sizeof($formula);
//                $strData = $this->setNames($strData, $formula, $blockCount);
//                $strData = $this->setAcronyms($strData, $formula, $blockCount);
//                $strData .= $formula[0][BlockTO::RESIDUE] . "\t";
//                $strData .= $formula[0][BlockTO::MASS] . "\t";
//                $strData .= $formula[0][BlockTO::LOSSES] . "\t";
//                $strData = $this->setReferences($strData, $formula, $blockCount);
//                file_put_contents(self::FILE_NAME, $strData, FILE_APPEND);
//            }
//            $start += CommonConstants::PAGING;
//            $arResult = $this->database->findMergeBlocks($start);
//        }
    }

    /**
     * @see IParser::reject()
     */
    public static function reject() {
        return new Reject('Not match blocks in right format');
    }

    private function setNames($strData, $formula, $blockCount) {
//        return $this->setData($strData, $formula, $blockCount, BlockTO::NAME);
    }

    private function setAcronyms(string $strData, $formula, int $blockCount) {
//        return $this->setData($strData, $formula, $blockCount, BlockTO::ACRONYM);
    }

    private function setData(string $strData, $formula, int $blockCount, string $type) {
        $index = 0;
        $strData .= $formula[$index][$type];
        for ($index = 1; $index < $blockCount; ++$index) {
            $strData .= '/' . $formula[$index][$type];
        }
        return $strData . "\t";
    }

    private function setReferences(string $strData, $formula, int $blockCount) {
        $index = 0;
        $strData .= ReferenceHelper::reference($formula[$index]['database'], $formula[$index]['identifier'], $formula[$index]['smiles']);
        for ($index = 1; $index < $blockCount; ++$index) {
            $strData .= '/' . ReferenceHelper::reference($formula[$index]['database'], $formula[$index]['identifier'], $formula[$index]['smiles']);
        }
        return $strData . PHP_EOL;
    }

    /**
     * @see AbstractCycloBranch::getLineLength()
     */
    protected function getLineLength() {
        return self::LENGTH;
    }

}
