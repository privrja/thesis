<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $nick;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     */
    private $apiToken;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\U2c", mappedBy="user", orphanRemoval=true)
     */
    private $u2container;

    public function __construct()
    {
        $this->u2container = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNick(): ?string
    {
        return $this->nick;
    }

    public function setNick(string $nick): self
    {
        $this->nick = $nick;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->nick;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $userRoles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $userRoles[] = 'ROLE_USER';

        return array_unique($userRoles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
//         $this->plainPassword = null;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    public function setApiToken(?string $apiToken): self
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    /**
     * @return Collection|U2c[]
     */
    public function getU2container(): Collection
    {
        return $this->u2container;
    }

    public function addU2container(U2c $u2container): self
    {
        if (!$this->u2container->contains($u2container)) {
            $this->u2container[] = $u2container;
            $u2container->setUser($this);
        }

        return $this;
    }

    public function removeU2container(U2c $u2container): self
    {
        if ($this->u2container->contains($u2container)) {
            $this->u2container->removeElement($u2container);
            // set the owning side to null (unless already changed)
            if ($u2container->getUser() === $this) {
                $u2container->setUser(null);
            }
        }

        return $this;
    }
}
