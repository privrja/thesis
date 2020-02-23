<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\B2sRepository")
 */
class B2s
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $sort;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sequence", inversedBy="b2s")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sequenceId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Block", inversedBy="b2s")
     * @ORM\JoinColumn(nullable=false)
     */
    private $blockId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function getSequenceId(): ?Sequence
    {
        return $this->sequenceId;
    }

    public function setSequenceId(?Sequence $sequenceId): self
    {
        $this->sequenceId = $sequenceId;

        return $this;
    }

    public function getBlockId(): ?Block
    {
        return $this->blockId;
    }

    public function setBlockId(?Block $blockId): self
    {
        $this->blockId = $blockId;

        return $this;
    }
}
