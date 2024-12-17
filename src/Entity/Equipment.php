<?php

namespace App\Entity;

use App\Repository\EquipmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipmentRepository::class)]
class Equipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    /**
     * @var Collection<int, Workspace>
     */
    #[ORM\ManyToMany(targetEntity: Workspace::class, inversedBy: 'equipmentId')]
    private Collection $workspace;

    public function __construct()
    {
        $this->workspace = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Collection<int, Workspace>
     */
    public function getWorkspace(): Collection
    {
        return $this->workspace;
    }

    public function addWorkspace(Workspace $workspace): static
    {
        if (!$this->workspace->contains($workspace)) {
            $this->workspace->add($workspace);
        }

        return $this;
    }

    public function removeWorkspace(Workspace $workspace): static
    {
        $this->workspace->removeElement($workspace);

        return $this;
    }
}
