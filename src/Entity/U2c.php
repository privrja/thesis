<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\U2cRepository")
 */
class U2c
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="u2container")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Container", inversedBy="c2users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $container;

    /**
     * @ORM\Column(type="smallint", options={"default": 0})
     */
    private $mode;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getContainer(): ?Container
    {
        return $this->container;
    }

    public function setContainer(?Container $container): self
    {
        $this->container = $container;

        return $this;
    }

    public function getMode(): ?int
    {
        return $this->mode;
    }

    public function setMode(int $mode): self
    {
        $this->mode = $mode;

        return $this;
    }
}
