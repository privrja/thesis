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
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private $nTerminal;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private $cTerminal;

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
}
