<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SequenceRepository")
 * @ORM\Table(uniqueConstraints={@UniqueConstraint(name="UX_SEQUENCE_NAME", columns={"sequence_name", "container_id"})})
 */
class Sequence implements JsonSerializable {
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, options={"default": "other"})
     */
    private $sequenceType;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sequenceName;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $sequence;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $sequenceOriginal;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sequenceFormula;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $sequenceMass;

    /**
     * @ORM\Column(type="string", length=4000, nullable=true)
     */
    private $sequenceSmiles;

    /**
     * @ORM\Column(type="string", length=4000, nullable=true)
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $decays;

    /**
     * @ORM\Column(type="integer")
     */
    private $uniqueBlockCount;

    /**
     * @ORM\Column(type="integer")
     */
    private $blockCount;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Modification", cascade={"persist"})
     */
    private $nModification;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Modification", cascade={"persist"})
     */
    private $cModification;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Modification", cascade={"persist"})
     */
    private $bModification;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\B2s", mappedBy="sequence", orphanRemoval=true, cascade={"persist"})
     */
    private $b2s;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Container", inversedBy="sequenceId")
     * @ORM\JoinColumn(nullable=false)
     */
    private $container;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\S2f", mappedBy="sequence", cascade={"persist", "remove"})
     */
    private $s2families;

    /**
     * @ORM\OneToMany(targetEntity=Organism::class, mappedBy="sequence", orphanRemoval=true)
     */
    private $S2Organisms;

    public function __construct() {
        $this->b2s = new ArrayCollection();
        $this->s2families = new ArrayCollection();
        $this->S2Organisms = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getSequenceType(): ?string {
        return $this->sequenceType;
    }

    public function setSequenceType(string $sequenceType): self {
        $this->sequenceType = $sequenceType;
        return $this;
    }

    public function getSequenceName(): ?string {
        return $this->sequenceName;
    }

    public function setSequenceName(string $sequenceName): self {
        $this->sequenceName = $sequenceName;
        return $this;
    }

    public function getSequenceFormula(): ?string {
        return $this->sequenceFormula;
    }

    public function setSequenceFormula(string $sequenceFormula): self {
        $this->sequenceFormula = $sequenceFormula;
        return $this;
    }

    public function getSequenceMass(): ?float {
        return $this->sequenceMass;
    }

    public function setSequenceMass(?float $sequenceMass): self {
        $this->sequenceMass = $sequenceMass;
        return $this;
    }

    public function getSequenceSmiles(): ?string {
        return $this->sequenceSmiles;
    }

    public function setSequenceSmiles(?string $sequenceSmiles): self {
        $this->sequenceSmiles = $sequenceSmiles;
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

    public function getDecays(): ?string {
        return $this->decays;
    }

    public function setDecays(?string $decays): self {
        $this->decays = $decays;
        return $this;
    }

    public function getNModification(): ?Modification {
        return $this->nModification;
    }

    public function setNModification(?Modification $nModification): self {
        $this->nModification = $nModification;
        return $this;
    }

    public function getCModification(): ?Modification {
        return $this->cModification;
    }

    public function setCModification(?Modification $cModification): self {
        $this->cModification = $cModification;
        return $this;
    }

    public function getBModification(): ?Modification {
        return $this->bModification;
    }

    public function setBModification(?Modification $bModification): self {
        $this->bModification = $bModification;
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
            $b2->setSequence($this);
        }

        return $this;
    }

    public function removeB2(B2s $b2): self {
        if ($this->b2s->contains($b2)) {
            $this->b2s->removeElement($b2);
            // set the owning side to null (unless already changed)
            if ($b2->getSequence() === $this) {
                $b2->setSequence(null);
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
     * @return Collection|S2f[]
     */
    public function getS2families(): Collection {
        return $this->s2families;
    }

    public function addS2family(S2f $s2family): self {
        if (!$this->s2families->contains($s2family)) {
            $this->s2families[] = $s2family;
            $s2family->setSequence($this);
        }

        return $this;
    }

    public function removeS2family(S2f $s2family): self {
        if ($this->s2families->contains($s2family)) {
            $this->s2families->removeElement($s2family);
            // set the owning side to null (unless already changed)
            if ($s2family->getSequence() === $this) {
                $s2family->setSequence(null);
            }
        }
        return $this;
    }

    public function emptyS2Family() {
        $this->s2families = new ArrayCollection();
    }

    public function emptyB2s() {
        $this->b2s = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getSequence(): ?string {
        return $this->sequence;
    }

    /**
     * @param string|null $sequence
     */
    public function setSequence(?string $sequence): void {
        $this->sequence = $sequence;
    }

    /**
     * @return string|null
     */
    public function getSequenceOriginal() {
        return $this->sequenceOriginal;
    }

    /**
     * @param string $sequenceOriginal
     */
    public function setSequenceOriginal($sequenceOriginal): void {
        $this->sequenceOriginal = $sequenceOriginal;
    }

    /**
     * @return string
     */
    public function getUsmiles() {
        return $this->usmiles;
    }

    /**
     * @param string $usmiles
     */
    public function setUsmiles($usmiles): void {
        $this->usmiles = $usmiles;
    }

    /**
     * @return int
     */
    public function getUniqueBlockCount() {
        return $this->uniqueBlockCount;
    }

    /**
     * @param int $blockCount
     */
    public function setUniqueBlockCount(int $blockCount): void {
        $this->uniqueBlockCount = $blockCount;
    }

    /**
     * @return mixed
     */
    public function getBlockCount() {
        return $this->blockCount;
    }

    /**
     * @param mixed $blockCount
     */
    public function setBlockCount($blockCount): void {
        $this->blockCount = $blockCount;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'sequenceName' => $this->sequenceName,
            'sequenceType' => $this->sequenceType,
            'sequence' => $this->sequence,
            'formula' => $this->sequenceFormula,
            'mass' => $this->sequenceMass,
            'smiles' => $this->sequenceSmiles,
            'uniqueSmiles' => $this->usmiles,
            'source' => $this->source,
            'identifier' => $this->identifier,
            'nModification' => $this->nModification,
            'cModification' => $this->cModification,
            'bModification' => $this->bModification,
        ];
    }

    /**
     * @return Collection|Organism[]
     */
    public function getS2Organisms(): Collection
    {
        return $this->S2Organisms;
    }

    public function addS2Organism(Organism $s2Organism): self
    {
        if (!$this->S2Organisms->contains($s2Organism)) {
            $this->S2Organisms[] = $s2Organism;
            $s2Organism->setSequence($this);
        }

        return $this;
    }

    public function removeS2Organism(Organism $s2Organism): self
    {
        if ($this->S2Organisms->removeElement($s2Organism)) {
            // set the owning side to null (unless already changed)
            if ($s2Organism->getSequence() === $this) {
                $s2Organism->setSequence(null);
            }
        }

        return $this;
    }

}
