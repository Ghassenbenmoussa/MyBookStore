<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\Author;
use App\Entity\Category;
use App\Entity\Editor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * Search books by multiple criteria
     */
    public function search(?string $title = null, ?Category $category = null, ?Editor $editor = null, ?Author $author = null): array
    {
        $qb = $this->createQueryBuilder('b')
            ->leftJoin('b.authors', 'a')
            ->leftJoin('b.category', 'c')
            ->leftJoin('b.editor', 'e');

        if ($title) {
            $qb->andWhere('b.title LIKE :title')
                ->setParameter('title', '%' . $title . '%');
        }

        if ($category) {
            $qb->andWhere('b.category = :category')
                ->setParameter('category', $category);
        }

        if ($editor) {
            $qb->andWhere('b.editor = :editor')
                ->setParameter('editor', $editor);
        }

        if ($author) {
            $qb->andWhere(':author MEMBER OF b.authors')
                ->setParameter('author', $author);
        }

        return $qb->orderBy('b.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all books ordered by title
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find books by category
     */
    public function findByCategory(Category $category): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.category = :category')
            ->setParameter('category', $category)
            ->orderBy('b.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find books by editor
     */
    public function findByEditor(Editor $editor): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.editor = :editor')
            ->setParameter('editor', $editor)
            ->orderBy('b.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find books in stock
     */
    public function findInStock(): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.stock > 0')
            ->orderBy('b.title', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

