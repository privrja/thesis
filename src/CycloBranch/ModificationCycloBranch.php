<?php

namespace App\CycloBranch;

use App\Entity\Container;
use App\Entity\Modification;
use App\Structure\ModificationTransformed;
use Doctrine\ORM\EntityManagerInterface;

class ModificationCycloBranch extends AbstractCycloBranch {

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
    public function import(Container $container, EntityManagerInterface $entityManager, array $okStack, array $errorStack): array {
        /** @var ModificationTransformed $item */
        foreach ($okStack as $item) {
            $res = $this->repository->findOneBy(['container' => $container->getId(), 'modificationName' => $item->getModificationName()]);
            if ($res) {
                $item->error = 'ERROR: Same name';
                array_push($errorStack, $item);
                continue;
            }
            $modification = new Modification();
            $modification->setContainer($container);
            $modification->setModificationName($item->getModificationName());
            $modification->setModificationFormula($item->getFormula());
            $modification->setModificationMass($item->getMass());
            $modification->setNTerminal($item->isNTerminal());
            $modification->setCTerminal($item->isCTerminal());
            $entityManager->persist($modification);
        }
        $entityManager->flush();
        return $errorStack;
    }

}
