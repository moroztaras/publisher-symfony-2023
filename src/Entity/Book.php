<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)] class Book
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column(type: 'integer')] private ?int $id;
    #[ORM\Column(type: 'string', length: 255)] private ?string $title;
    #[ORM\Column(type: 'string', length: 255)] private string $slug;
    #[ORM\Column(type: 'string', length: 255, nullable: true)] private ?string $image = null;
    #[ORM\Column(type: 'simple_array')] private array $authors;
    #[ORM\Column(type: 'date')] private \DateTimeInterface $publicationAt;
    #[ORM\Column(type: 'boolean')] private bool $meap = false;
    /**
         * @var Collection<Category>
         */ #[ORM\ManyToMany(targetEntity: Category::class)] private Collection $categories;
    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

       public function getId(): ?int
       {
           return $this->id;
       }

       public function getTitle(): string
       {
           return $this->title;
       }

       public function setTitle(string $title): self
       {
           $this->title = $title;

           return $this;
       }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): Book
    {
        $this->slug = $slug;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getAuthors(): array
    {
        return $this->authors;
    }

    public function setAuthors(array $authors): Book
    {
        $this->authors = $authors;

        return $this;
    }

    public function getPublicationAt(): \DateTimeInterface
    {
        return $this->publicationAt;
    }

    public function setPublicationAt(\DateTimeInterface $publicationAt): Book
    {
        $this->publicationAt = $publicationAt;

        return $this;
    }

    public function isMeap(): bool
    {
        return $this->meap;
    }

    public function setMeap(bool $meap): self
    {
        $this->meap = $meap;

        return $this;
    }

    /**
     * @return Collection<Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * @param Collection<Category> $categories
     *
     * @return $this
     */
    public function setCategories(Collection $categories): self
    {
        $this->categories = $categories;

        return $this;
    }
}
