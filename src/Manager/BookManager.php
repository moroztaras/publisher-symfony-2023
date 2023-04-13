<?php

namespace App\Manager;

use App\Entity\Book;
use App\Exception\CategoryNotFoundException;
use App\Model\BookListItem;
use App\Model\BookListResponse;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;

class BookManager
{
    public function __construct(
        private BookRepository $bookRepository,
        private CategoryRepository $categoryRepository,
    ) {
    }

    public function getBooksByCategory(int $categoryId): BookListResponse
    {
        if (!$this->categoryRepository->existsById($categoryId)) {
            throw new CategoryNotFoundException();
        }

        // We need to remap what we get from the repository to the model - BookListItem
        return new BookListResponse(array_map(
            [$this, 'map'],
            $this->bookRepository->findPublishedBooksByCategoryId($categoryId)
        ));
    }

    private function map(Book $book): BookListItem
    {
        return (new BookListItem())
            ->setId($book->getId())
            ->setTitle($book->getTitle())
            ->setSlug($book->getSlug())
            ->setAuthors($book->getAuthors())
            ->setMeap($book->isMeap())
        ;
    }
}
