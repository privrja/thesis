<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=S2oRepository::class)
 * @ORM\Table(name="`msb_s2o`")
 */
class S2o {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Organism::class, inversedBy="O2Seqeunces", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $organism;

    /**
     * @ORM\ManyToOne(targetEntity=Sequence::class, inversedBy="s2Organism")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sequence;

    public function getId(): ?int {
        return $this->id;
    }

    public function getOrganism(): ?Organism {
        return $this->organism;
    }

    public function setOrganism(?Organism $organism): self {
        $this->organism = $organism;
        return $this;
    }

    public function getSequence(): ?Sequence {
        return $this->sequence;
    }

    public function setSequence(?Sequence $sequence): self {
        $this->sequence = $sequence;
        return $this;
    }

}
