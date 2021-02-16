<?php

namespace App\CycloBranch;

use App\Entity\Modification;
use App\Smiles\Parser\BooleanParser;
use App\Smiles\Parser\Reject;

class ModificationCycloBranch extends AbstractCycloBranch {

    const FILE_NAME = './uploads/modifications.txt';

    const NAME = 0;
    const FORMULA = 1;
    const MASS = 2;
    const N_TERMINAL = 3;
    const C_TERMINAL = 4;
    const LENGTH = 5;

    /**
     * @param string $line
     * @return Reject
     * @see AbstractCycloBranch::parse()
     */
    public function parse(string $line) {
        // TODO
        $arItems = $this->validateLine($line);
        if ($arItems === false) {
            return self::reject();
        }

        $booleanParser = new BooleanParser();
        $booleanNTerminalResult = $booleanParser->parse($arItems[self::N_TERMINAL]);
        $booleanCTerminalResult = $booleanParser->parse($arItems[self::C_TERMINAL]);
        if (!$booleanCTerminalResult->isAccepted() || !$booleanNTerminalResult->isAccepted()) {
            return self::reject();
        }

//        $modification = new ModificationTO($arItems[self::NAME], $arItems[self::FORMULA], $arItems[self::MASS], $booleanCTerminalResult->getResult(), $booleanNTerminalResult->getResult());
//        return new Accept([$modification->asEntity()], '');
    }

    /**
     * @see AbstractCycloBranch::reject()
     */
    public static function reject() {
        return new Reject('Not match modification in right format');
    }

    /**
     * @see AbstractCycloBranch::download()
     */
    public function download() {
        $this->data = '';
        /** @var Modification[] $arResult */
        $arResult = $this->repository->findBy(['container' => $this->containerId]);
        if (!empty($arResult)) {
            foreach ($arResult as $modification) {
                $this->data .= $modification->getModificationName() . "\t"
                    . $modification->getModificationFormula() . "\t"
                    . $modification->getModificationMass() . "\t"
                    . $modification->getNTerminal() . "\t"
                    . $modification->getCTerminal() . PHP_EOL;
            }
        }
    }

}
