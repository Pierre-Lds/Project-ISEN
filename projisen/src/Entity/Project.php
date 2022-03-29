<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    /**
     * @Assert\Length(
     *     min = 10,
     *     max = 100,
     *     minMessage = "Enter more than 10 characters",
     *     maxMessage = "Enter less than 100 characters",
     * )
     */
    private $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $technical_domains;

    #[ORM\Column(type: 'boolean')]
    private $is_taken;

    #[ORM\ManyToOne(targetEntity: Thematic::class, inversedBy: 'id_thematic')]
    #[ORM\JoinColumn(nullable: false)]
    private $id_thematic;

    #[ORM\ManyToMany(targetEntity: ProfessionalDomain::class, inversedBy: 'id_professional_domain')]
    private $id_professional_domain;

    #[ORM\ManyToOne(targetEntity: Staff::class, inversedBy: 'id_teacher')]
    #[ORM\JoinColumn(nullable: false)]
    private $id_teacher;

    #[ORM\Column(type: 'integer')]
    private $year;

    #[ORM\OneToMany(mappedBy: 'id_project', targetEntity: Student::class)]
    private $students;

    #[ORM\OneToMany(mappedBy: 'id_project_1', targetEntity: ProjectWishes::class)]
    private $id_project_1;

    #[ORM\OneToMany(mappedBy: 'id_project_2', targetEntity: ProjectWishes::class)]
    private $id_project_2;

    #[ORM\OneToMany(mappedBy: 'id_project_3', targetEntity: ProjectWishes::class)]
    private $id_project_3;

    public function __construct() {
        $this->id_professional_domain = new ArrayCollection();
        $this->students = new ArrayCollection();
        $this->id_project_1 = new ArrayCollection();
        $this->id_project_2 = new ArrayCollection();
        $this->id_project_3 = new ArrayCollection();
    }

    public function __toString(): string {
        return $this->title;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getTitle(): ?string {
        return $this->title;
    }

    public function setTitle(string $title): self {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): self {
        $this->description = $description;
        return $this;
    }

    public function getTechnicalDomains(): ?string {
        return $this->technical_domains;
    }

    public function setTechnicalDomains(?string $technical_domains): self {
        $this->technical_domains = $technical_domains;
        return $this;
    }

    public function getIsTaken(): ?bool {
        return $this->is_taken;
    }

    public function setIsTaken(bool $is_taken): self {
        $this->is_taken = $is_taken;
        return $this;
    }

    public function getIdThematic(): ?Thematic {
        return $this->id_thematic;
    }

    public function setIdThematic(?Thematic $id_thematic): self {
        $this->id_thematic = $id_thematic;
        return $this;
    }

    /**
     * @return Collection<int, ProfessionalDomain>
     */
    public function getIdProfessionalDomain(): Collection {
        return $this->id_professional_domain;
    }

    public function addIdProfessionalDomain(ProfessionalDomain $idProfessionalDomain): self {
        if (!$this->id_professional_domain->contains($idProfessionalDomain)) {
            $this->id_professional_domain[] = $idProfessionalDomain;
        }
        return $this;
    }

    public function removeIdProfessionalDomain(ProfessionalDomain $idProfessionalDomain): self {
        $this->id_professional_domain->removeElement($idProfessionalDomain);
        return $this;
    }

    public function getIdTeacher(): ?Staff {
        return $this->id_teacher;
    }

    public function setIdTeacher(?Staff $id_teacher): self {
        $this->id_teacher = $id_teacher;
        return $this;
    }

    public function getYear(): ?int {
        return $this->year;
    }

    public function setYear(int $year): self {
        $this->year = $year;
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
            $student->setIdProject($this);
        }

        return $this;
    }

    public function removeStudent(Student $student): self
    {
        if ($this->students->removeElement($student)) {
            // set the owning side to null (unless already changed)
            if ($student->getIdProject() === $this) {
                $student->setIdProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProjectWishes>
     */
    public function getIdProject1(): Collection
    {
        return $this->id_project_1;
    }

    public function addIdProject1(ProjectWishes $idProject1): self
    {
        if (!$this->id_project_1->contains($idProject1)) {
            $this->id_project_1[] = $idProject1;
            $idProject1->setIdProject1($this);
        }

        return $this;
    }

    public function removeIdProject1(ProjectWishes $idProject1): self
    {
        if ($this->id_project_1->removeElement($idProject1)) {
            // set the owning side to null (unless already changed)
            if ($idProject1->getIdProject1() === $this) {
                $idProject1->setIdProject1(null);
            }
        }

        return $this;
    }

    public function getIdProject2(): Collection
    {
        return $this->id_project_2;
    }

    public function addIdProject2(ProjectWishes $idProject2): self
    {
        if (!$this->id_project_2->contains($idProject2)) {
            $this->id_project_2[] = $idProject2;
            $idProject2->setIdProject1($this);
        }

        return $this;
    }

    public function removeIdProject2(ProjectWishes $idProject2): self
    {
        if ($this->id_project_2->removeElement($idProject2)) {
            // set the owning side to null (unless already changed)
            if ($idProject2->getIdProject2() === $this) {
                $idProject2->setIdProject2(null);
            }
        }

        return $this;
    }

    public function getIdProject3(): Collection
    {
        return $this->id_project_3;
    }

    public function addIdProject3(ProjectWishes $idProject3): self
    {
        if (!$this->id_project_3->contains($idProject3)) {
            $this->id_project_3[] = $idProject3;
            $idProject3->setIdProject1($this);
        }

        return $this;
    }

    public function removeIdProject3(ProjectWishes $idProject3): self
    {
        if ($this->id_project_3->removeElement($idProject3)) {
            // set the owning side to null (unless already changed)
            if ($idProject3->getIdProject3() === $this) {
                $idProject3->setIdProject3(null);
            }
        }

        return $this;
    }
}
