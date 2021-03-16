<?php

namespace App\Entity;

use App\Repository\ConditionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ConditionRepository::class)
 * @ORM\Table(name="`condition`")
 */
class Condition {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", length=65535)
     */
    private $text;

    public function getId(): ?int {
        return $this->id;
    }

    public function getText(): ?string {
        return $this->text;
    }

    public function setText(string $text): self {
        $this->text = $text;
        return $this;
    }

}
