<?php

namespace App\Entity;

use App\Repository\DeadlineRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DeadlineRepository::class)]
class Deadline {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'date', nullable: true)]
    private $pair;

    #[ORM\Column(type: 'date', nullable: true)]
    private $new_projects;

    #[ORM\Column(type: 'date', nullable: true)]
    private $choose_projects_1;

    #[ORM\Column(type: 'date', nullable: true)]
    private $choose_projects_2;

    public function getId(): ?int {
        return $this->id;
    }

    public function getPair(): ?\DateTimeInterface {
        return $this->pair;
    }

    public function setPair(?\DateTimeInterface $pair): self {
        $this->pair = $pair;
        return $this;
    }

    public function getNewProjects(): ?\DateTimeInterface {
        return $this->new_projects;
    }

    public function setNewProjects(?\DateTimeInterface $new_projects): self {
        $this->new_projects = $new_projects;
        return $this;
    }

    public function getChooseProjects1(): ?\DateTimeInterface {
        return $this->choose_projects_1;
    }

    public function setChooseProjects1(?\DateTimeInterface $choose_projects_1): self {
        $this->choose_projects_1 = $choose_projects_1;
        return $this;
    }

    public function getChooseProjects2(): ?\DateTimeInterface {
        return $this->choose_projects_2;
    }

    public function setChooseProjects2(?\DateTimeInterface $choose_projects_2): self {
        $this->choose_projects_2 = $choose_projects_2;
        return $this;
    }
}
