<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SequenceFamilyRepository")
 * @ORM\Table(uniqueConstraints={@UniqueConstraint(name="UX_SEQUENCE_FAMILY_NAME", columns={"sequence_family_name", "container_id"})}, name="`msb_sequence_family`")
 */
class SequenceFamily implements JsonSerializable {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sequenceFamilyName;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\S2f", mappedBy="family")
     */
    private $f2sequences;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Container", inversedBy="sequenceFamilies")
     * @ORM\JoinColumn(nullable=false)
     */
    private $container;

    public function __construct() {
        $this->f2sequences = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getSequenceFamilyName(): ?string {
        return $this->sequenceFamilyName;
    }

    public function setSequenceFamilyName(string $sequenceFamilyName): self {
        $this->sequenceFamilyName = $sequenceFamilyName;
        return $this;
    }

    /**
     * @return Collection|S2f[]
     */
    public function getF2sequences(): Collection {
        return $this->f2sequences;
    }

    public function addF2sequence(S2f $f2sequence): self {
        if (!$this->f2sequences->contains($f2sequence)) {
            $this->f2sequences[] = $f2sequence;
            $f2sequence->setFamily($this);
        }
        return $this;
    }

    public function removeF2sequence(S2f $f2sequence): self {
        if ($this->f2sequences->contains($f2sequence)) {
            $this->f2sequences->removeElement($f2sequence);
            // set the owning side to null (unless already changed)
            if ($f2sequence->getFamily() === $this) {
                $f2sequence->setFamily(null);
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
     * @inheritDoc
     */
    public function jsonSerialize() {
        return ['id' => $this->id, 'family' => $this->sequenceFamilyName];
    }

}
