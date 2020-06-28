<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BlockRepository")
 * @ORM\Table(indexes={@Index(name="IDX_BLOCK_ID", columns={"id"})})
 */
class Block
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
    private $name;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $acronym;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $residue;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $mass;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $losses;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $smiles;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $usmiles;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $source;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $identifier;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\B2s", mappedBy="blockId")
     */
    private $b2s;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Container", inversedBy="blockId")
     * @ORM\JoinColumn(nullable=false)
     */
    private $container;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\B2f", mappedBy="blockId")
     */
    private $b2families;

    public function __construct()
    {
        $this->b2s = new ArrayCollection();
        $this->b2families = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAcronym(): ?string
    {
        return $this->acronym;
    }

    public function setAcronym(string $acronym): self
    {
        $this->acronym = $acronym;

        return $this;
    }

    public function getResidue(): ?string
    {
        return $this->residue;
    }

    public function setResidue(string $residue): self
    {
        $this->residue = $residue;

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

    public function getLosses(): ?string
    {
        return $this->losses;
    }

    public function setLosses(?string $losses): self
    {
        $this->losses = $losses;

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
            $b2->setBlock($this);
        }

        return $this;
    }

    public function removeB2(B2s $b2): self
    {
        if ($this->b2s->contains($b2)) {
            $this->b2s->removeElement($b2);
            // set the owning side to null (unless already changed)
            if ($b2->getBlock() === $this) {
                $b2->setBlock(null);
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

    /**
     * @return Collection|B2f[]
     */
    public function getB2families(): Collection
    {
        return $this->b2families;
    }

    public function addB2family(B2f $b2family): self
    {
        if (!$this->b2families->contains($b2family)) {
            $this->b2families[] = $b2family;
            $b2family->setBlock($this);
        }

        return $this;
    }

    public function removeB2family(B2f $b2family): self
    {
        if ($this->b2families->contains($b2family)) {
            $this->b2families->removeElement($b2family);
            // set the owning side to null (unless already changed)
            if ($b2family->getBlock() === $this) {
                $b2family->setBlock(null);
            }
        }

        return $this;
    }

    public function getUsmiles(): ?string
    {
        return $this->usmiles;
    }

    public function setUsmiles(?string $usmiles): self
    {
        $this->usmiles = $usmiles;

        return $this;
    }

}
