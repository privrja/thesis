<?php

namespace App\CycloBranch;

use App\Base\ReferenceHelper;
use App\Entity\Block;
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
        // TODO
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
    }

    /**
     * @see IParser::reject()
     */
    public static function reject() {
        return new Reject('Not match blocks in right format');
    }

}
