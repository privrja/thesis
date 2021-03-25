<?php

namespace App\Entity;

use App\Repository\SetupRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SetupRepository::class)
 * @ORM\Table(name="`msb_setup`")
 */
class Setup {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10, options={"default":"name"})
     */
    private $similarity;

    public function getId(): ?int {
        return $this->id;
    }

    public function getSimilarity(): ?string {
        return $this->similarity;
    }

    public function setSimilarity(string $similarity): self {
        $this->similarity = $similarity;
        return $this;
    }

}
