<?php

namespace App\Entity;

use App\Repository\OrganismRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass=OrganismRepository::class)
 * @ORM\Table(uniqueConstraints={@UniqueConstraint(name="UX_ORGANISM_NAME", columns={"organism", "container_id"})}, name="`msb_organism`")
 */
class Organism implements JsonSerializable {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Container::class, inversedBy="organisms")
     * @ORM\JoinColumn(nullable=false)
     */
    private $container;

    /**
     * @ORM\OneToMany(targetEntity=S2O::class, mappedBy="organism")
     */
    private $O2Seqeunces;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $organism;

    public function __construct() {
        $this->O2Seqeunces = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getContainer(): ?Container {
        return $this->container;
    }

    public function setContainer(?Container $container): self {
        $this->container = $container;
        return $this;
    }

    /**
     * @return Collection|S2o[]
     */
    public function getO2Seqeunces(): Collection {
        return $this->O2Seqeunces;
    }

    public function addO2Seqeunce(S2o $o2Seqeunce): self {
        if (!$this->O2Seqeunces->contains($o2Seqeunce)) {
            $this->O2Seqeunces[] = $o2Seqeunce;
            $o2Seqeunce->setOrganism($this);
        }
        return $this;
    }

    public function removeO2Seqeunce(S2o $o2Seqeunce): self {
        if ($this->O2Seqeunces->removeElement($o2Seqeunce)) {
            // set the owning side to null (unless already changed)
            if ($o2Seqeunce->getOrganism() === $this) {
                $o2Seqeunce->setOrganism(null);
            }
        }
        return $this;
    }

    public function getOrganism(): ?string {
        return $this->organism;
    }

    public function setOrganism(string $organism): self {
        $this->organism = $organism;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize() {
        return ['id' => $this->id, 'organism' => $this->organism];
    }

}
