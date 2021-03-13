<?php

namespace App\Entity;

use App\Repository\OrganismRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrganismRepository::class)
 */
class Organism
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Sequence::class, inversedBy="S2Organisms")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sequence;

    /**
     * @ORM\ManyToOne(targetEntity=Organism::class, inversedBy="O2Sequences")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organism;

    /**
     * @ORM\OneToMany(targetEntity=Organism::class, mappedBy="organism", orphanRemoval=true)
     */
    private $O2Sequences;

    /**
     * @ORM\ManyToOne(targetEntity=Container::class, inversedBy="organisms")
     * @ORM\JoinColumn(nullable=false)
     */
    private $container;

    public function __construct()
    {
        $this->O2Sequences = new ArrayCollection();
    }

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

    public function getOrganism(): ?self
    {
        return $this->organism;
    }

    public function setOrganism(?self $organism): self
    {
        $this->organism = $organism;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getO2Sequences(): Collection
    {
        return $this->O2Sequences;
    }

    public function addO2Sequence(self $o2Sequence): self
    {
        if (!$this->O2Sequences->contains($o2Sequence)) {
            $this->O2Sequences[] = $o2Sequence;
            $o2Sequence->setOrganism($this);
        }

        return $this;
    }

    public function removeO2Sequence(self $o2Sequence): self
    {
        if ($this->O2Sequences->removeElement($o2Sequence)) {
            // set the owning side to null (unless already changed)
            if ($o2Sequence->getOrganism() === $this) {
                $o2Sequence->setOrganism(null);
            }
        }

        return $this;
    }

    public function getContainer(): ?Container
    {
        return $this->container;
    }

    public function setContainer(?Container $container): self
    {
        $this->container = $container;

        return $this;
    }
}
