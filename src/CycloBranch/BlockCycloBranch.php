<?php

namespace App\CycloBranch;

use App\Base\ReferenceHelper;
use App\Entity\Block;
use App\Entity\Container;
use App\Structure\BlockTransformed;
use Doctrine\ORM\EntityManagerInterface;

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
                $this->data .= str_replace(',', '.', $block->getBlockName()) . self::TABULATOR
                    . str_replace(',', '.', $block->getAcronym()) . self::TABULATOR
                    . $block->getResidue() . self::TABULATOR
                    . $block->getBlockMass() . self::TABULATOR
                    . $block->getLosses() . self::TABULATOR
                    . ReferenceHelper::reference($block->getSource(), $block->getIdentifier(), $block->getUsmiles())
                    . PHP_EOL;
            }
        }
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function import(Container $container, EntityManagerInterface $entityManager, array $okStack, array $errorStack): array {
        /** @var BlockTransformed $item */
        foreach ($okStack as $item) {
            $res = $this->repository->findOneBy(['container' => $container->getId(), 'acronym' => $item->getAcronym()]);
            if ($res) {
                $item->error = 'ERROR: Same acronym';
                array_push($errorStack,  $item);
                continue;
            }
            $block = new Block();
            $block->setContainer($container);
            $block->setBlockName(str_replace('.', ',' ,$item->getBlockName()));
            $block->setAcronym(str_replace('.', ',', $item->getAcronym()));
            $block->setResidue($item->getFormula());
            $block->setBlockMass($item->getMass());
            $block->setLosses($item->getLosses());
            $block->setSource($item->getSource());
            $block->setIdentifier($item->getIdentifier());
            $block->setBlockSmiles($item->getSmiles());
            $block->setUsmiles($item->getUSmiles());
            $block->setIsPolyketide($item->isPolyketide);
            $entityManager->persist($block);
        }
        $entityManager->flush();
        return $errorStack;
    }
}

