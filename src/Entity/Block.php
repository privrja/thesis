<?php

namespace App\Entity;

use App\Constant\EntityColumnsEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BlockRepository")
 * @ORM\Table(uniqueConstraints={@UniqueConstraint(name="UX_BLOCK_ACRONYM", columns={"acronym", "container_id"})})
 */
class Block implements JsonSerializable {
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $blockName;

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
    private $blockMass;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $losses;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $blockSmiles;

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
     * @ORM\Column(type="boolean", nullable=false, options={"default": 0})
     */
    private $isPolyketide;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\B2s", mappedBy="block")
     */
    private $b2s;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Container", inversedBy="blockId")
     * @ORM\JoinColumn(nullable=false)
     */
    private $container;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\B2f", mappedBy="block")
     */
    private $b2families;

    public function __construct() {
        $this->b2s = new ArrayCollection();
        $this->b2families = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getBlockName(): ?string {
        return $this->blockName;
    }

    public function setBlockName(string $blockName): self {
        $this->blockName = $blockName;

        return $this;
    }

    public function getAcronym(): ?string {
        return $this->acronym;
    }

    public function setAcronym(string $acronym): self {
        $this->acronym = $acronym;

        return $this;
    }

    public function getResidue(): ?string {
        return $this->residue;
    }

    public function setResidue(string $residue): self {
        $this->residue = $residue;

        return $this;
    }

    public function getBlockMass(): ?float {
        return $this->blockMass;
    }

    public function setBlockMass(?float $blockMass): self {
        $this->blockMass = $blockMass;

        return $this;
    }

    public function getLosses(): ?string {
        return $this->losses;
    }

    public function setLosses(?string $losses): self {
        $this->losses = $losses;

        return $this;
    }

    public function getBlockSmiles(): ?string {
        return $this->blockSmiles;
    }

    public function setBlockSmiles(?string $blockSmiles): self {
        $this->blockSmiles = $blockSmiles;

        return $this;
    }

    public function getSource(): ?int {
        return $this->source;
    }

    public function setSource(?int $source): self {
        $this->source = $source;

        return $this;
    }

    public function getIdentifier(): ?string {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): self {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @return Collection|B2s[]
     */
    public function getB2s(): Collection {
        return $this->b2s;
    }

    public function addB2(B2s $b2): self {
        if (!$this->b2s->contains($b2)) {
            $this->b2s[] = $b2;
            $b2->setBlock($this);
        }

        return $this;
    }

    public function removeB2(B2s $b2): self {
        if ($this->b2s->contains($b2)) {
            $this->b2s->removeElement($b2);
            // set the owning side to null (unless already changed)
            if ($b2->getBlock() === $this) {
                $b2->setBlock(null);
            }
        }

        return $this;
    }

    public function getContainer(): ?Container {
        return $this->container;
    }

    public function setContainer(?Container $container): self {
        $this->container = $container;

        return $this;
    }

    /**
     * @return Collection|B2f[]
     */
    public function getB2families(): Collection {
        return $this->b2families;
    }

    public function addB2family(B2f $b2family): self {
        if (!$this->b2families->contains($b2family)) {
            $this->b2families[] = $b2family;
            $b2family->setBlock($this);
        }

        return $this;
    }

    public function removeB2family(B2f $b2family): self {
        if ($this->b2families->contains($b2family)) {
            $this->b2families->removeElement($b2family);
            // set the owning side to null (unless already changed)
            if ($b2family->getBlock() === $this) {
                $b2family->setBlock(null);
            }
        }

        return $this;
    }

    public function getUsmiles(): ?string {
        return $this->usmiles;
    }

    public function setUsmiles(?string $usmiles): self {
        $this->usmiles = $usmiles;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsPolyketide() {
        return $this->isPolyketide;
    }

    /**
     * @param mixed $isPolyketide
     */
    public function setIsPolyketide($isPolyketide): void {
        $this->isPolyketide = $isPolyketide;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize() {
        return [EntityColumnsEnum::ID => $this->id,
            EntityColumnsEnum::BLOCK_NAME => $this->blockName,
            EntityColumnsEnum::ACRONYM => $this->acronym,
            EntityColumnsEnum::FORMULA => $this->residue,
            EntityColumnsEnum::MASS => $this->blockMass,
            EntityColumnsEnum::LOSSES => $this->losses,
            EntityColumnsEnum::SMILES => $this->blockSmiles,
            EntityColumnsEnum::UNIQUE_SMILES => $this->usmiles,
            EntityColumnsEnum::SOURCE => $this->source,
            EntityColumnsEnum::IDENTIFIER => $this->identifier
        ];
    }

}
