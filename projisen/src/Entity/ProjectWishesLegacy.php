<?php

namespace App\Entity;

use App\Repository\ProjectWishesLegacyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectWishesLegacyRepository::class)]
class ProjectWishesLegacy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $id_project_1;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $id_project_2;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $id_project_3;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $id_student;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $year;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdProject1(): ?int
    {
        return $this->id_project_1;
    }

    public function setIdProject1(?int $id_project_1): self
    {
        $this->id_project_1 = $id_project_1;

        return $this;
    }

    public function getIdProject2(): ?int
    {
        return $this->id_project_2;
    }

    public function setIdProject2(?int $id_project_2): self
    {
        $this->id_project_2 = $id_project_2;

        return $this;
    }

    public function getIdProject3(): ?int
    {
        return $this->id_project_3;
    }

    public function setIdProject3(?int $id_project_3): self
    {
        $this->id_project_3 = $id_project_3;

        return $this;
    }

    public function getIdStudent(): ?int
    {
        return $this->id_student;
    }

    public function setIdStudent(?int $id_student): self
    {
        $this->id_student = $id_student;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): self
    {
        $this->year = $year;

        return $this;
    }
}
