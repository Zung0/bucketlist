<?php

namespace App\Entity;

use App\Repository\WishRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use http\Message;
use Symfony\Component\Validator\Constraints as Assert;
use function Symfony\Component\Clock\now;

#[ORM\Entity(repositoryClass: WishRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Wish
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Assert\NotBlank(Message:('Vous devez remplir ce champ !'))]
    #[Assert\Length(min: 5, minMessage: 'Le nom doit avoir au moins 5 charactères !')]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    #[Assert\Assert\NotBlank(Message:('Vous devez remplir ce champ !'))]
    #[Assert\Length(min: 3, minMessage: 'Le nom doit avoir au moins 3 charactères !')]
    private ?string $author = null;

    #[ORM\Column]
    private ?bool $isPublished = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreated = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateUpdated = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function isIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): static
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    #[ORM\PrePersist()]
    public function setDateCreated(): \DateTime
    {
        return $this->dateCreated = new \DateTime();
    }

    public function getDateUpdated(): ?\DateTimeInterface
    {
        return $this->dateUpdated;
    }
    #[ORM\PreUpdate()]
    public function setDateUpdated():  static
    {
         $this->dateUpdated = new \DateTime();
         return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }
    #[ORM\PreRemove]
    public function deleteImage(): static
    {
        if($this->getImage()&& file_exists('images'.$this->getImage()))
        {
            //unlink permet de détruire le fichier initial.
            unlink('images'.$this->getImage());

        }
    return $this;
    }
}
