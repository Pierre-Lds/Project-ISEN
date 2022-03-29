<?php

namespace App\Entity;

use App\Repository\ThematicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ThematicRepository::class)]
class Thematic {
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

    #[ORM\OneToMany(mappedBy: 'id_thematic', targetEntity: Project::class)]
    private $id_thematic;

    public function __construct() {
        $this->id_thematic = new ArrayCollection();
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
    public function getIdThematic(): Collection {
        return $this->id_thematic;
    }

    public function addIdThematic(Project $idThematic): self {
        if (!$this->id_thematic->contains($idThematic)) {
            $this->id_thematic[] = $idThematic;
            $idThematic->setIdThematic($this);
        }
        return $this;
    }

    public function removeIdThematic(Project $idThematic): self {
        if ($this->id_thematic->removeElement($idThematic)) {
            // set the owning side to null (unless already changed)
            if ($idThematic->getIdThematic() === $this) {
                $idThematic->setIdThematic(null);
            }
        }
        return $this;
    }
}
