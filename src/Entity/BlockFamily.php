<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BlockFamilyRepository")
 */
class BlockFamily
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
     * @ORM\OneToMany(targetEntity="App\Entity\B2f", mappedBy="familyId")
     */
    private $f2blocks;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Container", inversedBy="blockFamilies")
     * @ORM\JoinColumn(nullable=false)
     */
    private $container;

    public function __construct()
    {
        $this->f2blocks = new ArrayCollection();
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

    /**
     * @return Collection|B2f[]
     */
    public function getF2blocks(): Collection
    {
        return $this->f2blocks;
    }

    public function addF2block(B2f $f2block): self
    {
        if (!$this->f2blocks->contains($f2block)) {
            $this->f2blocks[] = $f2block;
            $f2block->setFamily($this);
        }

        return $this;
    }

    public function removeF2block(B2f $f2block): self
    {
        if ($this->f2blocks->contains($f2block)) {
            $this->f2blocks->removeElement($f2block);
            // set the owning side to null (unless already changed)
            if ($f2block->getFamily() === $this) {
                $f2block->setFamily(null);
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
