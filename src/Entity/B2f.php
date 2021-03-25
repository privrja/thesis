<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\B2fRepository")
 * @ORM\Table(name="`msb_b2f`")
 */
class B2f {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Block", inversedBy="b2families")
     * @ORM\JoinColumn(nullable=false)
     */
    private $block;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BlockFamily", inversedBy="f2blocks", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $family;

    public function getId(): ?int {
        return $this->id;
    }

    public function getBlock(): ?Block {
        return $this->block;
    }

    public function setBlock(?Block $block): self {
        $this->block = $block;
        return $this;
    }

    public function getFamily(): ?BlockFamily {
        return $this->family;
    }

    public function setFamily(?BlockFamily $family): self {
        $this->family = $family;
        return $this;
    }

}
