<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\S2fRepository")
 */
class S2f
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sequence", inversedBy="s2families")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sequence;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SequenceFamily", inversedBy="f2sequences")
     * @ORM\JoinColumn(nullable=false)
     */
    private $family;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSequence(): ?Sequence
    {
        return $this->sequence;
    }

    public function setSequence(?Sequence $sequence): self
    {
        $this->sequence = $sequence;

        return $this;
    }

    public function getFamily(): ?SequenceFamily
    {
        return $this->family;
    }

    public function setFamily(?SequenceFamily $family): self
    {
        $this->family = $family;

        return $this;
    }
}
