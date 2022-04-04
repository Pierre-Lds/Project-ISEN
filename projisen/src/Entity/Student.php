<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
class Student implements UserInterface, PasswordAuthenticatedUserInterface {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $username;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string' )]
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
    private $is_main_student;

    #[ORM\Column(type: 'integer')]
    /**
     * @Assert\Range(
     *     min = 2000,
     *     max = 3000,
     *     notInRangeMessage = "You must enter a value between 2000 and 3000",
     * )
     */
    private $graduation_year;

    #[ORM\OneToOne(targetEntity: self::class, cascade: ['persist', 'remove'])]
    private $id_pair = null;

    #[ORM\ManyToOne(targetEntity: ProfessionalDomain::class, inversedBy: 'students')]
    #[ORM\JoinColumn(nullable: false)]
    private $id_professional_domain;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'students')]
    private $id_project;

    #[ORM\OneToOne(mappedBy: 'id_main_student', targetEntity: ProjectWishes::class, cascade: ['persist', 'remove'])]
    private $projectWishes;

    public function getId(): ?int {
        return $this->id;
    }

    public function __toString(): string {
        return $this->first_name." ".$this->last_name;
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

    public function getIsMainStudent(): ?bool {
        return $this->is_main_student;
    }

    public function setIsMainStudent(bool $is_main_student): self {
        $this->is_main_student = $is_main_student;
        return $this;
    }

    public function getGraduationYear(): ?int {
        return $this->graduation_year;
    }

    public function setGraduationYear(int $graduation_year): self {
        $this->graduation_year = $graduation_year;
        return $this;
    }

    public function getIdPair(): ?self {
        return $this->id_pair;
    }

    public function setIdPair(?self $id_pair): self {
        $this->id_pair = $id_pair;
        return $this;
    }

    public function getIdProfessionalDomain(): ?ProfessionalDomain
    {
        return $this->id_professional_domain;
    }

    public function setIdProfessionalDomain(?ProfessionalDomain $id_professional_domain): self
    {
        $this->id_professional_domain = $id_professional_domain;

        return $this;
    }

    public function getIdProject(): ?Project
    {
        return $this->id_project;
    }

    public function setIdProject(?Project $id_project): self
    {
        $this->id_project = $id_project;

        return $this;
    }

    public function getProjectWishes(): ?ProjectWishes
    {
        return $this->projectWishes;
    }

    public function setProjectWishes(?ProjectWishes $projectWishes): self
    {
        // unset the owning side of the relation if necessary
        if ($projectWishes === null && $this->projectWishes !== null) {
            $this->projectWishes->setIdMainStudent(null);
        }

        // set the owning side of the relation if necessary
        if ($projectWishes !== null && $projectWishes->getIdMainStudent() !== $this) {
            $projectWishes->setIdMainStudent($this);
        }

        $this->projectWishes = $projectWishes;

        return $this;
    }
}
