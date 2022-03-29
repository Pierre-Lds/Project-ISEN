<?php

namespace App\Entity;

use App\Repository\ProfessionalDomainRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProfessionalDomainRepository::class)]
class ProfessionalDomain {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    /**
     * @Assert\Length(
     *     min = 2,
     *     max = 50,
     *     minMessage = "Enter more than 2 characters",
     *     maxMessage = "Enter less than 50 characters",
     * )
     */
    private $name;

    #[ORM\ManyToMany(targetEntity: Project::class, mappedBy: 'id_professional_domain')]
    private $id_professional_domain;

    #[ORM\OneToMany(mappedBy: 'id_professional_domain', targetEntity: Student::class)]
    private $students;


    public function __construct() {
        $this->id_professional_domain = new ArrayCollection();
        $this->professional_domain = new ArrayCollection();
        $this->students = new ArrayCollection();
    }

    public function __toString(): string {
        return $this->name;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getIdProfessionalDomain(): Collection {
        return $this->id_professional_domain;
    }

    public function addIdProfessionalDomain(Project $idProfessionalDomain): self {
        if (!$this->id_professional_domain->contains($idProfessionalDomain)) {
            $this->id_professional_domain[] = $idProfessionalDomain;
            $idProfessionalDomain->addIdProfessionalDomain($this);
        }
        return $this;
    }

    public function removeIdProfessionalDomain(Project $idProfessionalDomain): self {
        if ($this->id_professional_domain->removeElement($idProfessionalDomain)) {
            $idProfessionalDomain->removeIdProfessionalDomain($this);
        }
        return $this;
    }

    /**
     * @return Collection<int, Student>
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(Student $student): self
    {
        if (!$this->students->contains($student)) {
            $this->students[] = $student;
            $student->setIdProfessionalDomain($this);
        }

        return $this;
    }

    public function removeStudent(Student $student): self
    {
        if ($this->students->removeElement($student)) {
            // set the owning side to null (unless already changed)
            if ($student->getIdProfessionalDomain() === $this) {
                $student->setIdProfessionalDomain(null);
            }
        }

        return $this;
    }

}
