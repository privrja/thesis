<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ModificationRepository")
 */
class Modification
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
    private $modificationName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $modificationFormula;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $modificationMass;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private $nTerminal;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private $cTerminal;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Container", inversedBy="modificationId")
     * @ORM\JoinColumn(nullable=false)
     */
    private $container;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModificationName(): ?string
    {
        return $this->modificationName;
    }

    public function setModificationName(string $modificationName): self
    {
        $this->modificationName = $modificationName;

        return $this;
    }

    public function getModificationFormula(): ?string
    {
        return $this->modificationFormula;
    }

    public function setModificationFormula(string $modificationFormula): self
    {
        $this->modificationFormula = $modificationFormula;

        return $this;
    }

    public function getModificationMass(): ?float
    {
        return $this->modificationMass;
    }

    public function setModificationMass(?float $modificationMass): self
    {
        $this->modificationMass = $modificationMass;

        return $this;
    }

    public function getNTerminal(): ?bool
    {
        return $this->nTerminal;
    }

    public function setNTerminal(bool $nTerminal): self
    {
        $this->nTerminal = $nTerminal;

        return $this;
    }

    public function getCTerminal(): ?bool
    {
        return $this->cTerminal;
    }

    public function setCTerminal(bool $cTerminal): self
    {
        $this->cTerminal = $cTerminal;

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
