<?php

namespace App\CycloBranch;

use App\Base\ReferenceHelper;
use App\Base\SequenceHelper;
use App\Entity\Block;
use App\Entity\Container;
use App\Entity\Modification;
use App\Entity\Sequence;
use App\Enum\SequenceEnum;
use App\Structure\SequenceTransformed;
use Doctrine\ORM\EntityManagerInterface;

class SequenceCycloBranch extends AbstractCycloBranch {

    /**
     * @see AbstractCycloBranch::download()
     */
    public function download(): string {
        $this->data = '';
        /** @var Sequence[] $arResult */
        $arResult = $this->repository->findBy(['container' => $this->containerId]);
        if (!empty($arResult)) {
            foreach ($arResult as $sequence) {
                $this->data .= $sequence->getSequenceType() . self::TABULATOR
                    . $sequence->getSequenceName() . self::TABULATOR
                    . $sequence->getSequenceFormula() . self::TABULATOR
                    . $sequence->getSequenceMass() . self::TABULATOR
                    . $sequence->getSequence() . self::TABULATOR
                    . (($sequence->getNModification() !== null) ? $sequence->getNModification()->getModificationName() : '') . self::TABULATOR
                    . (($sequence->getCModification() !== null) ? $sequence->getCModification()->getModificationName() : '') . self::TABULATOR
                    . (($sequence->getBModification() !== null) ? $sequence->getBModification()->getModificationName() : '') . self::TABULATOR
                    . ReferenceHelper::reference($sequence->getSource(), $sequence->getIdentifier(), $sequence->getSequenceSmiles())
                    . PHP_EOL;
            }
        }
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function import(Container $container, EntityManagerInterface $entityManager, array $okStack, array $errorStack): array {
        /** @var SequenceTransformed $item */
        foreach ($okStack as $item) {
            $res = $this->repository->findOneBy(['container' => $container->getId(), 'sequenceName' => $item->getSequenceName()]);
            if ($res) {
                $item->error = 'ERROR: Same name';
                array_push($errorStack, $item);
                continue;
            }
            $sequence = new Sequence();
            $sequence->setContainer($container);
            $sequence->setSequenceName($item->getSequenceName());
            $sequence->setSequenceType($item->getSequenceType());
            $sequence->setSequenceFormula($item->getFormula());
            $sequence->setSequenceMass($item->getMass());
            $sequence->setSequence($item->getSequence());
            $sequence->setSequenceSmiles($item->getUsmiles());
            $sequence->setSource($item->getSource());
            $sequence->setIdentifier($item->getIdentifier());

            $modificationRepository = $entityManager->getRepository(Modification::class);
            if (!empty($item->getNModification())) {
                /** @var Modification $res */
                $res = $modificationRepository->findOneBy(['container' => $container->getId(), 'modificationName' => $item->getNModification()]);
                if (isset($res)) {
                    $sequence->setNModification($res);
                } else {
                    $item->error = 'ERROR: Not found N modification';
                    array_push($errorStack, $item);
                    continue;
                }
            }
            if (!empty($item->getCModification())) {
                $res = $modificationRepository->findOneBy(['container' => $container->getId(), 'modificationName' => $item->getCModification()]);
                if (isset($res)) {
                    $sequence->setCModification($res);
                } else {
                    $item->error = 'ERROR: Not found C modification';
                    array_push($errorStack, $item);
                    continue;
                }
            }
            if (!empty($item->getBModification())) {
                $res = $modificationRepository->findOneBy(['container' => $container->getId(), 'modificationName' => $item->getBModification()]);
                if (isset($res)) {
                    $sequence->setBModification($res);
                } else {
                    $item->error = 'ERROR: Not found B modification';
                    array_push($errorStack, $item);
                    continue;
                }
            }
            $sequenceHelper = new SequenceHelper($sequence->getSequence(), SequenceEnum::$backValues[$sequence->getSequenceType()], []);
            $b2s = $sequenceHelper->findBlocks($container, $entityManager->getRepository(Block::class));
            if (empty($b2s)) {
                $item->error = 'ERROR: Not all blocks used sequence is in container';
                array_push($errorStack, $item);
                continue;
            }
            $uniqueBlocks = [];
            $cntUniqueBlocks = 0;
            foreach ($b2s as $connection) {
                $sequence->addB2($connection);
                if (!isset($uniqueBlocks[$connection->getBlock()->getId()])) {
                    $uniqueBlocks[$connection->getBlock()->getId()] = 1;
                    $cntUniqueBlocks++;
                }
            }
            $sequence->setUniqueBlockCount($cntUniqueBlocks);
            $entityManager->persist($sequence);
        }
        $entityManager->flush();
        return $errorStack;
    }

}
