<?php

namespace App\Entity;

use App\Repository\S2ORepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=S2ORepository::class)
 */
class S2O {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Sequence::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $sequence;

    /**
     * @ORM\ManyToOne(targetEntity=Organism::class, inversedBy="O2Seqeunces")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organism;

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

    public function getOrganism(): ?Organism {
        return $this->organism;
    }

    public function setOrganism(?Organism $organism): self {
        $this->organism = $organism;
        return $this;
    }

}
