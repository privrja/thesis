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
     * @ORM\Column(type="smallint")
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="containers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

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

    public function __construct()
    {
        $this->sequenceId = new ArrayCollection();
        $this->blockId = new ArrayCollection();
        $this->modificationId = new ArrayCollection();
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

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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
}
