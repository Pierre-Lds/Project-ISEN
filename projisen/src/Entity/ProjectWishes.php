<?php

namespace App\Entity;

use App\Repository\ProjectWishesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectWishesRepository::class)]
class ProjectWishes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'id_project_1')]
    private $id_project_1;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'id_project_2')]
    private $id_project_2;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'id_project_3')]
    private $id_project_3;

    #[ORM\OneToOne(inversedBy: 'projectWishes', targetEntity: Student::class, cascade: ['persist', 'remove'])]
    private $id_main_student;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdProject1(): ?Project
    {
        return $this->id_project_1;
    }

    public function setIdProject1(?Project $id_project_1): self
    {
        $this->id_project_1 = $id_project_1;

        return $this;
    }

    public function getIdProject2(): ?Project
    {
        return $this->id_project_2;
    }

    public function setIdProject2(?Project $id_project_2): self
    {
        $this->id_project_2 = $id_project_2;

        return $this;
    }

    public function getIdProject3(): ?Project
    {
        return $this->id_project_3;
    }

    public function setIdProject3(?Project $id_project_3): self
    {
        $this->id_project_3 = $id_project_3;

        return $this;
    }

    public function getIdMainStudent(): ?Student
    {
        return $this->id_main_student;
    }

    public function setIdMainStudent(?Student $id_main_student): self
    {
        $this->id_main_student = $id_main_student;

        return $this;
    }
}
