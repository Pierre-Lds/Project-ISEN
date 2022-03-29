<?php

namespace App\Entity;

use App\Repository\StaffRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StaffRepository::class)]
class Staff implements UserInterface, PasswordAuthenticatedUserInterface {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $username;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    /**
     * @Assert\Length(
     *     min = 8,
     *     max = 30,
     *     minMessage = "Enter more than 8 characters",
     *     maxMessage = "Enter less than 30 characters",
     * )
     */
    private $password;

    #[ORM\Column(type: 'string', length: 255)]
    /**
     * @Assert\Length(
     *     min = 2,
     *     max = 50,
     *     minMessage = "Enter more than 2 characters",
     *     maxMessage = "Enter less than 50 characters",
     * )
     */
    private $first_name;

    #[ORM\Column(type: 'string', length: 255)]
    /**
     * @Assert\Length(
     *     min = 2,
     *     max = 50,
     *     minMessage = "Enter more than 2 characters",
     *     maxMessage = "Enter less than 50 characters",
     * )
     */
    private $last_name;

    #[ORM\Column(type: 'boolean')]
    private $is_admin;

    #[ORM\OneToMany(mappedBy: 'id_teacher', targetEntity: Project::class)]
    private $id_teacher;

    public function __construct() {
        $this->id_teacher = new ArrayCollection();
    }

    public function __toString(): string {
        return $this->first_name." ".$this->last_name;
    }

    public function getId(): ?int {
        return $this->id;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string {
        return (string) $this->username;
    }

    public function setUsername(string $username): self {
        $this->username = $username;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array {
        $roles = $this->roles;
        return array_unique($roles);
    }

    public function setRoles(array $roles): self {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string {
        return $this->password;
    }

    public function setPassword(string $password): self {
        $this->password = $password;
        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials() {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self {
        $this->first_name = $first_name;
        return $this;
    }

    public function getLastName(): ?string {
        return $this->last_name;
    }

    public function setLastName(string $last_name): self {
        $this->last_name = $last_name;
        return $this;
    }

    public function getIsAdmin(): ?bool {
        return $this->is_admin;
    }

    public function setIsAdmin(bool $is_admin): self {
        $this->is_admin = $is_admin;
        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getIdTeacher(): Collection {
        return $this->id_teacher;
    }

    public function addIdTeacher(Project $idTeacher): self {
        if (!$this->id_teacher->contains($idTeacher)) {
            $this->id_teacher[] = $idTeacher;
            $idTeacher->setIdTeacher($this);
        }
        return $this;
    }

    public function removeIdTeacher(Project $idTeacher): self {
        if ($this->id_teacher->removeElement($idTeacher)) {
            // set the owning side to null (unless already changed)
            if ($idTeacher->getIdTeacher() === $this) {
                $idTeacher->setIdTeacher(null);
            }
        }
        return $this;
    }
}
