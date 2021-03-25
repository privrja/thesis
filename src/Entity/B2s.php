<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\B2sRepository")
 * @ORM\Table(name="`msb_b2s`")
 */
class B2s {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sequence", inversedBy="b2s")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sequence;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Block", inversedBy="b2s", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $block;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Block", cascade={"persist"})
     */
    private $nextBlock;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": 0})
     *
     */
    private $isBranch;

    /**
     * @ORM\Column(type="integer")
     */
    private $blockOriginalId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Block", cascade={"persist"})
     */
    private $branchReference;

    /**
     * @ORM\Column(type="integer")
     */
    private $sort;

    public function getId(): ?int {
        return $this->id;
    }

    public function getSequence(): ?Sequence {
        return $this->sequence;
    }

    public function setSequence(?Sequence $sequence): self {
        $this->sequence = $sequence;
        return $this;
    }

    public function getBlock(): ?Block {
        return $this->block;
    }

    public function setBlock(?Block $block): self {
        $this->block = $block;
        return $this;
    }

    public function getNextBlock(): ?Block {
        return $this->nextBlock;
    }

    public function setNextBlock(?Block $nextBlock): self {
        $this->nextBlock = $nextBlock;
        return $this;
    }

    public function getIsBranch(): ?bool {
        return $this->isBranch;
    }

    public function setIsBranch(bool $isBranch): self {
        $this->isBranch = $isBranch;
        return $this;
    }

    public function getBranchReference(): ?Block {
        return $this->branchReference;
    }

    public function setBranchReference(?Block $branchReference): self {
        $this->branchReference = $branchReference;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBlockOriginalId() {
        return $this->blockOriginalId;
    }

    /**
     * @param mixed $blockOriginalId
     */
    public function setBlockOriginalId($blockOriginalId): void {
        $this->blockOriginalId = $blockOriginalId;
    }

    /**
     * @return mixed
     */
    public function getSort() {
        return $this->sort;
    }

    /**
     * @param mixed $sort
     */
    public function setSort($sort): void {
        $this->sort = $sort;
    }

}
