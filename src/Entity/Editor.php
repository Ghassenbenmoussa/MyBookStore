<?php
namespace App\Entity;
use App\Repository\EditorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: EditorRepository::class)]
class Editor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(length: 150)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 150)]
    private ?string $name = null;
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;
    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Url]
    private ?string $website = null;
    #[ORM\OneToMany(targetEntity: Book::class, mappedBy: 'editor')]
    private Collection $books;
    public function __construct()
    {
        $this->books = new ArrayCollection();
    }
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
    public function getAddress(): ?string
    {
        return $this->address;
    }
    public function setAddress(?string $address): static
    {
        $this->address = $address;
        return $this;
    }
    public function getWebsite(): ?string
    {
        return $this->website;
    }
    public function setWebsite(?string $website): static
    {
        $this->website = $website;
        return $this;
    }
    public function getBooks(): Collection
    {
        return $this->books;
    }
    public function addBook(Book $book): static
    {
        if (!$this->books->contains($book)) {
            $this->books->add($book);
            $book->setEditor($this);
        }
        return $this;
    }
    public function removeBook(Book $book): static
    {
        if ($this->books->removeElement($book)) {
            if ($book->getEditor() === $this) {
                $book->setEditor(null);
            }
        }
        return $this;
    }
    public function __toString(): string
    {
        return $this->name ?? '';
    }
}
