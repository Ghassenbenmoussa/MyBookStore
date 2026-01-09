<?php
namespace App\Entity;
use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $title = null;
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private ?string $price = null;
    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    private ?int $stock = null;
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $coverImage = null;
    #[ORM\ManyToOne(inversedBy: 'books')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Category $category = null;
    #[ORM\ManyToOne(inversedBy: 'books')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Editor $editor = null;
    #[ORM\ManyToMany(targetEntity: Author::class, inversedBy: 'books')]
    #[Assert\Count(min: 1, minMessage: 'A book must have at least one author')]
    private Collection $authors;
    public function __construct()
    {
        $this->authors = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getTitle(): ?string
    {
        return $this->title;
    }
    public function setTitle(string $title): static
    {
        $this->title = $title;
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
    public function getPrice(): ?string
    {
        return $this->price;
    }
    public function setPrice(string $price): static
    {
        $this->price = $price;
        return $this;
    }
    public function getStock(): ?int
    {
        return $this->stock;
    }
    public function setStock(int $stock): static
    {
        $this->stock = $stock;
        return $this;
    }
    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }
    public function setCoverImage(?string $coverImage): static
    {
        $this->coverImage = $coverImage;
        return $this;
    }
    public function getCategory(): ?Category
    {
        return $this->category;
    }
    public function setCategory(?Category $category): static
    {
        $this->category = $category;
        return $this;
    }
    public function getEditor(): ?Editor
    {
        return $this->editor;
    }
    public function setEditor(?Editor $editor): static
    {
        $this->editor = $editor;
        return $this;
    }
    public function getAuthors(): Collection
    {
        return $this->authors;
    }
    public function addAuthor(Author $author): static
    {
        if (!$this->authors->contains($author)) {
            $this->authors->add($author);
        }
        return $this;
    }
    public function removeAuthor(Author $author): static
    {
        $this->authors->removeElement($author);
        return $this;
    }
    public function isInStock(): bool
    {
        return $this->stock > 0;
    }
    public function __toString(): string
    {
        return $this->title ?? '';
    }
}
