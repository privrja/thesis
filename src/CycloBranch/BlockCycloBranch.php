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
    public function import(Container $container, EntityManagerInterface $entityManager, array $okStack, array $errorStack): array {
        /** @var BlockTransformed $item */
        foreach ($okStack as $item) {
            $res = $this->repository->findOneBy(['container' => $container->getId(), 'acronym' => $item->getAcronym()]);
            if ($res) {
                array_push($errorStack, $item);
                continue;
            }
            $block = new Block();
            $block->setContainer($container);
            $block->setBlockName($item->getBlockName());
            $block->setAcronym($item->getAcronym());
            $block->setResidue($item->getFormula());
            $block->setBlockMass($item->getMass());
            $block->setLosses($item->getLosses());
            $block->setSource($item->getSource());
            $block->setIdentifier($item->getIdentifier());
            $block->setBlockSmiles($item->getSmiles());
            $block->setUsmiles($item->getUSmiles());
            $entityManager->persist($block);
        }
        $entityManager->flush();
        return $errorStack;
    }
}

