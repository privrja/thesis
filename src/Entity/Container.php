<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ContainerRepository")
 */
class Container
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
     * @ORM\Column(type="smallint", options={"default": 0})
     */
    private $visibility;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Block", mappedBy="container", orphanRemoval=true)
     */
    private $blockId;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Modification", mappedBy="container", orphanRemoval=true)
     */
    private $modificationId;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sequence", mappedBy="container", orphanRemoval=true)
     */
    private $sequenceId;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BlockFamily", mappedBy="containerId", orphanRemoval=true)
     */
    private $blockFamilies;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SequenceFamily", mappedBy="containerId", orphanRemoval=true)
     */
    private $sequenceFamilies;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\U2c", mappedBy="containerId", orphanRemoval=true)
     */
    private $c2users;

    public function __construct()
    {
        $this->sequenceId = new ArrayCollection();
        $this->blockId = new ArrayCollection();
        $this->modificationId = new ArrayCollection();
        $this->blockFamilies = new ArrayCollection();
        $this->sequenceFamilies = new ArrayCollection();
        $this->c2users = new ArrayCollection();
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

    public function getVisibility(): ?int
    {
        return $this->visibility;
    }

    public function setVisibility(int $visibility): self
    {
        $this->visibility = $visibility;

        return $this;
    }

    /**
     * @return Collection|Block[]
     */
    public function getBlockId(): Collection
    {
        return $this->blockId;
    }

    public function addBlockId(Block $blockId): self
    {
        if (!$this->blockId->contains($blockId)) {
            $this->blockId[] = $blockId;
            $blockId->setContainer($this);
        }

        return $this;
    }

    public function removeBlockId(Block $blockId): self
    {
        if ($this->blockId->contains($blockId)) {
            $this->blockId->removeElement($blockId);
            // set the owning side to null (unless already changed)
            if ($blockId->getContainer() === $this) {
                $blockId->setContainer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Modification[]
     */
    public function getModificationId(): Collection
    {
        return $this->modificationId;
    }

    public function addModificationId(Modification $modificationId): self
    {
        if (!$this->modificationId->contains($modificationId)) {
            $this->modificationId[] = $modificationId;
            $modificationId->setContainer($this);
        }

        return $this;
    }

    public function removeModificationId(Modification $modificationId): self
    {
        if ($this->modificationId->contains($modificationId)) {
            $this->modificationId->removeElement($modificationId);
            // set the owning side to null (unless already changed)
            if ($modificationId->getContainer() === $this) {
                $modificationId->setContainer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Sequence[]
     */
    public function getSequenceId(): Collection
    {
        return $this->sequenceId;
    }

    public function addSequenceId(Sequence $sequenceId): self
    {
        if (!$this->sequenceId->contains($sequenceId)) {
            $this->sequenceId[] = $sequenceId;
            $sequenceId->setContainer($this);
        }

        return $this;
    }

    public function removeSequenceId(Sequence $sequenceId): self
    {
        if ($this->sequenceId->contains($sequenceId)) {
            $this->sequenceId->removeElement($sequenceId);
            // set the owning side to null (unless already changed)
            if ($sequenceId->getContainer() === $this) {
                $sequenceId->setContainer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|BlockFamily[]
     */
    public function getBlockFamilies(): Collection
    {
        return $this->blockFamilies;
    }

    public function addBlockFamily(BlockFamily $blockFamily): self
    {
        if (!$this->blockFamilies->contains($blockFamily)) {
            $this->blockFamilies[] = $blockFamily;
            $blockFamily->setContainer($this);
        }

        return $this;
    }

    public function removeBlockFamily(BlockFamily $blockFamily): self
    {
        if ($this->blockFamilies->contains($blockFamily)) {
            $this->blockFamilies->removeElement($blockFamily);
            // set the owning side to null (unless already changed)
            if ($blockFamily->getContainer() === $this) {
                $blockFamily->setContainer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|SequenceFamily[]
     */
    public function getSequenceFamilies(): Collection
    {
        return $this->sequenceFamilies;
    }

    public function addSequenceFamily(SequenceFamily $sequenceFamily): self
    {
        if (!$this->sequenceFamilies->contains($sequenceFamily)) {
            $this->sequenceFamilies[] = $sequenceFamily;
            $sequenceFamily->setContainer($this);
        }

        return $this;
    }

    public function removeSequenceFamily(SequenceFamily $sequenceFamily): self
    {
        if ($this->sequenceFamilies->contains($sequenceFamily)) {
            $this->sequenceFamilies->removeElement($sequenceFamily);
            // set the owning side to null (unless already changed)
            if ($sequenceFamily->getContainer() === $this) {
                $sequenceFamily->setContainer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|U2c[]
     */
    public function getC2users(): Collection
    {
        return $this->c2users;
    }

    public function addC2user(U2c $c2user): self
    {
        if (!$this->c2users->contains($c2user)) {
            $this->c2users[] = $c2user;
            $c2user->setContainer($this);
        }

        return $this;
    }

    public function removeC2user(U2c $c2user): self
    {
        if ($this->c2users->contains($c2user)) {
            $this->c2users->removeElement($c2user);
            // set the owning side to null (unless already changed)
            if ($c2user->getContainer() === $this) {
                $c2user->setContainer(null);
            }
        }

        return $this;
    }
}
