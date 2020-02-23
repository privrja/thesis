<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SequenceRepository")
 */
class Sequence
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $formula;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $mass;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sequence;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $smiles;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $source;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $identifier;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $decays;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Modification")
     */
    private $nModificationId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Modification")
     */
    private $cModificationId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Modification")
     */
    private $bModificationId;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\B2s", mappedBy="sequenceId", orphanRemoval=true)
     */
    private $b2s;

    public function __construct()
    {
        $this->b2s = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFormula(): ?string
    {
        return $this->formula;
    }

    public function setFormula(string $formula): self
    {
        $this->formula = $formula;

        return $this;
    }

    public function getMass(): ?float
    {
        return $this->mass;
    }

    public function setMass(?float $mass): self
    {
        $this->mass = $mass;

        return $this;
    }

    public function getSequence(): ?string
    {
        return $this->sequence;
    }

    public function setSequence(?string $sequence): self
    {
        $this->sequence = $sequence;

        return $this;
    }

    public function getSmiles(): ?string
    {
        return $this->smiles;
    }

    public function setSmiles(?string $smiles): self
    {
        $this->smiles = $smiles;

        return $this;
    }

    public function getSource(): ?int
    {
        return $this->source;
    }

    public function setSource(?int $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getDecays(): ?string
    {
        return $this->decays;
    }

    public function setDecays(?string $decays): self
    {
        $this->decays = $decays;

        return $this;
    }

    public function getNModificationId(): ?Modification
    {
        return $this->nModificationId;
    }

    public function setNModificationId(?Modification $nModificationId): self
    {
        $this->nModificationId = $nModificationId;

        return $this;
    }

    public function getCModificationId(): ?Modification
    {
        return $this->cModificationId;
    }

    public function setCModificationId(?Modification $cModificationId): self
    {
        $this->cModificationId = $cModificationId;

        return $this;
    }

    public function getBModificationId(): ?Modification
    {
        return $this->bModificationId;
    }

    public function setBModificationId(?Modification $bModificationId): self
    {
        $this->bModificationId = $bModificationId;

        return $this;
    }

    /**
     * @return Collection|B2s[]
     */
    public function getB2s(): Collection
    {
        return $this->b2s;
    }

    public function addB2(B2s $b2): self
    {
        if (!$this->b2s->contains($b2)) {
            $this->b2s[] = $b2;
            $b2->setSequenceId($this);
        }

        return $this;
    }

    public function removeB2(B2s $b2): self
    {
        if ($this->b2s->contains($b2)) {
            $this->b2s->removeElement($b2);
            // set the owning side to null (unless already changed)
            if ($b2->getSequenceId() === $this) {
                $b2->setSequenceId(null);
            }
        }

        return $this;
    }

}
