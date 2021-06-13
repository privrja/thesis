<?php

namespace App\Base;

use App\Entity\B2s;
use App\Entity\Block;
use App\Entity\Container;
use App\Enum\SequenceEnum;
use App\Repository\BlockRepository;
use InvalidArgumentException;

class SequenceHelper {

    /** @var string */
    private $sequenceType;

    /** @var string */
    private $sequence;

    /** @var array */
    private $blocks;

    /** @var int */
    private $length;

    /*** @var string */
    private $originalSequence;

    /** @var int */
    private $original;

    private $indexStart = 0;
    private $indexEnd = 0;
    private $acronym = '';
    private $branch = false;
    private $branchIndexStart = 0;
    private $branchIndexEnd = 0;
    private $mapAcronym = [];
    private $originalIndexStart = 0;
    private $originalIndexEnd = 0;

    /**
     * SequenceHelper constructor.
     * @param string $sequence
     * @param int $sequenceType
     * @param Block[] $blocks
     */
    public function __construct(string $sequence, int $sequenceType, array $blocks) {
        $this->sequence = $sequence;
        $this->sequenceType = $sequenceType;
        $this->blocks = $blocks;
        $this->length = strlen($sequence);
    }

    function getBlock(Container $container, BlockRepository $blockRepository, string $acronym) {
        if (!isset($this->mapAcronym[$acronym])) {
            $block = $blockRepository->findOneBy(['container' => $container->getId(), 'acronym' => $acronym]);
            $this->mapAcronym[$acronym] = $block;
            return $block;
        } else {
            return $this->mapAcronym[$acronym];
        }
    }

    /**
     * @param Container $container
     * @param BlockRepository $blockRepository
     * @return B2s[]
     */
    function findBlocks(Container $container, BlockRepository $blockRepository): array {
        /** @var B2s[] $res */
        $res = [];
        $len = 0;
        while ($this->nextBlock()) {
            $b2s = new B2s();
            $block = $this->getBlock($container, $blockRepository, $this->acronym);
            if (!isset($block)) {
                return [];
            }
            $b2s->setBlockOriginalId($len);
            $b2s->setSort($len);
            $b2s->setBlock($block);
            $b2s->setIsBranch($this->branch);
            $branchNextAcronym = $this->branchNext(true);
            if (isset($branchNextAcronym)) {
                $branchNext = $this->getBlock($container, $blockRepository, $branchNextAcronym);
                if (!isset($branchNext)) {
                    return [];
                }
                $b2s->setBranchReference($branchNext);
            }
            $nextAcronym = $this->findNextAcronym();
            if (isset($nextAcronym)) {
                $next = $this->getBlock($container, $blockRepository, $nextAcronym);
                if (!isset($next)) {
                    return [];
                }
                $b2s->setNextBlock($next);
            }
            array_push($res, $b2s);
            $len++;
        }
        $this->addCyclicReference($res, $len);
        return $res;
    }

    /**
     * @param string $originalSequence
     * @return B2s[]
     */
    public function sequenceBlocksStructure(string $originalSequence): array {
        /** @var B2s[] $res */
        $res = [];
        $len = 0;
        $this->originalSequence = $originalSequence;
        while ($this->nextBlock() && $this->nextOriginal()) {
            $b2s = new B2s();
            $block = $this->findBlock($this->acronym);
            $b2s->setBlock($block);
            $b2s->setBlockOriginalId($this->original);
            $b2s->setIsBranch($this->branch);
            $b2s->setBranchReference($this->branchNext());
            $b2s->setNextBlock($this->findNext());
            $b2s->setSort($len);
            array_push($res, $b2s);
            $len++;
        }
        $this->addCyclicReference($res, $len);
        return $res;
    }

    function addCyclicReference(&$res, $len) {
        if ($this->sequenceType === SequenceEnum::BRANCH_CYCLIC || $this->sequenceType === SequenceEnum::CYCLIC || $this->sequenceType === SequenceEnum::CYCLIC_POLYKETIDE) {
            for ($i = $len - 1; $i > 0; $i--) {
                if ($res[$i]->getIsBranch() === false) {
                    for ($j = 0; $j < $this->length; $j++) {
                        if ($res[$j]->getIsBranch() === false) {
                            $res[$i]->setNextBlock($res[$j]->getBlock());
                            break;
                        }
                    }
                    break;
                }
            }
        }
    }

    private function findNext() {
        $next = $this->findNextAcronym();
        if (!isset($next)) {
            return null;
        }
        return $this->findBlock($next);
    }

    private function findNextAcronym() {
        if ($this->branch === false) {
            $start = strpos($this->sequence, '[', $this->branchIndexEnd);
            if ($start === false) {
                return null;
            }
            $end = strpos($this->sequence, ']', $start);
            if ($start === false) {
                return null;
            }
            return substr($this->sequence, $start + 1, $end - $start - 1);
        } else {
            return null;
        }
    }

    private function nextBlock() {
        $index = strpos($this->sequence, '[', $this->indexEnd);
        if ($index === false) {
            return false;
        }
        $this->indexStart = $index;
        $index = strpos($this->sequence, ']', $this->indexStart);
        if ($index === false) {
            return false;
        }
        $this->indexEnd = $index;
        $this->acronym = substr($this->sequence, $this->indexStart + 1, $this->indexEnd - $this->indexStart - 1);
        $this->branch = $this->findBranch();
        return true;
    }

    private function nextOriginal() {
        $index = strpos($this->originalSequence, '[', $this->originalIndexEnd);
        if ($index === false) {
            return false;
        }
        $this->originalIndexStart = $index;
        $index = strpos($this->originalSequence, ']', $this->originalIndexStart);
        if ($index === false) {
            return false;
        }
        $this->originalIndexEnd = $index;
        $this->original = substr($this->originalSequence, $this->originalIndexStart + 1, $this->originalIndexEnd - $this->originalIndexStart - 1);
        return true;
    }

    private function findNextBranch(bool $onlyAcronym = false) {
        $branch = $this->findNextBranchAcronym();
        if (!isset($branch)) {
            return null;
        }
        if ($onlyAcronym) {
            return $branch;
        }
        return $this->findBlock($branch);
    }

    private function findNextBranchAcronym() {
        $nextIndexStart = $this->indexEnd + 2;
        if ($nextIndexStart >= $this->length || substr($this->sequence, $nextIndexStart, 1) == ")") {
            return null;
        } else {
            $nextIndexEnd = strpos($this->sequence, ']', $nextIndexStart);
            if ($nextIndexEnd === null) {
                return null;
            }
            return substr($this->sequence, $nextIndexStart + 1, $nextIndexEnd - $nextIndexStart - 1);
        }
    }

    private function branchNext(bool $onlyAcronym = false) {
        if ($this->possibleBranch() === false) {
            return null;
        }
        return $this->findNextBranch($onlyAcronym);
    }

    private function findBlock(string $acronym) {
        foreach ($this->blocks as $block) {
            if ($block->getAcronym() == $acronym) {
                return $block;
            }
        }
        return null;
    }

    private function findBranch() {
        switch ($this->sequenceType) {
            default:
            case SequenceEnum::LINEAR:
            case SequenceEnum::CYCLIC:
            case SequenceEnum::LINEAR_POLYKETIDE:
            case SequenceEnum::CYCLIC_POLYKETIDE:
            case SequenceEnum::OTHER:
                break;
            case SequenceEnum::BRANCH_CYCLIC:
            case SequenceEnum::BRANCHED:
                $left = substr_count($this->sequence, '(', 0, $this->indexStart);
                $right = substr_count($this->sequence, ')', 0, $this->indexStart);
                if ($left !== $right) {
                    $start = strripos($this->sequence, '(', -($this->length - $this->indexStart));
                    if ($start === null) {
                        break;
                    }
                    $end = strpos($this->sequence, ')', $start);
                    if ($end === null) {
                        throw new InvalidArgumentException('Wrong braces');
                    }
                    $this->branchIndexStart = $start;
                    $this->branchIndexEnd = $end;
                    if ($start === $this->indexStart - 1) {
                        return false;
                    }
                    return true;
                }
                break;
        }
        $this->branchIndexEnd = $this->indexEnd;
        $this->branchIndexStart = $this->indexEnd;
        return false;
    }

    private function possibleBranch() {
        switch ($this->sequenceType) {
            default:
            case SequenceEnum::LINEAR:
            case SequenceEnum::CYCLIC:
            case SequenceEnum::LINEAR_POLYKETIDE:
            case SequenceEnum::CYCLIC_POLYKETIDE:
            case SequenceEnum::OTHER:
                break;
            case SequenceEnum::BRANCH_CYCLIC:
            case SequenceEnum::BRANCHED:
                $left = substr_count($this->sequence, '(', 0, $this->indexStart);
                $right = substr_count($this->sequence, ')', 0, $this->indexStart);
                return $left !== $right;
        }
        return false;
    }

}
