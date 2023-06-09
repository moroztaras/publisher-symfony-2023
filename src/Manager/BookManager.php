<?php

namespace App\Manager;

use App\Entity\Book;
use App\Entity\BookToBookFormat;
use App\Entity\Category;
use App\Exception\CategoryNotFoundException;
use App\Mapper\BookMapper;
use App\Model\BookDetails;
use App\Model\BookFormat;
use App\Model\BookListItem;
use App\Model\BookListResponse;
use App\Model\Category as BookCategoryModel;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use App\Repository\ReviewRepository;
use Doctrine\Common\Collections\Collection;

class BookManager
{
    public function __construct(
        private BookRepository $bookRepository,
        private CategoryRepository $categoryRepository,
        private ReviewRepository $reviewRepository,
        private RatingManager $ratingManager)
    {
    }

    public function getBooksByCategory(int $categoryId): BookListResponse
    {
        if (!$this->categoryRepository->existsById($categoryId)) {
            throw new CategoryNotFoundException();
        }

        // We need to remap what we get from the repository to the model - BookListItem
        return new BookListResponse(array_map(
            fn (Book $book) => BookMapper::map($book, new BookListItem()),
            $this->bookRepository->findPublishedBooksByCategoryId($categoryId)
        ));
    }

    public function getBookById(int $id): BookDetails
    {
        $book = $this->bookRepository->getById($id);
        $reviews = $this->reviewRepository->countByBookId($id);

        // Remaps data from the database to the model
        $categories = $book->getCategories()
            ->map(fn (Category $bookCategory) => new BookCategoryModel(
                $bookCategory->getId(), $bookCategory->getTitle(), $bookCategory->getSlug()
            ));

        return BookMapper::map($book, new BookDetails())
            ->setRating($this->ratingManager->calcReviewRatingForBook($id, $reviews))
            ->setReviews($reviews)
            ->setFormats($this->mapFormats($book->getFormats()))
            ->setCategories($categories->toArray());
    }

    /**
     * @param Collection<BookToBookFormat> $formats
     */
    private function mapFormats(Collection $formats): array
    {
        return $formats->map(fn (BookToBookFormat $formatJoin) => (new BookFormat())
            ->setId($formatJoin->getFormat()->getId())
            ->setTitle($formatJoin->getFormat()->getTitle())
            ->setDescription($formatJoin->getFormat()->getDescription())
            ->setComment($formatJoin->getFormat()->getComment())
            ->setPrice($formatJoin->getPrice())
            ->setDiscountPercent($formatJoin->getDiscountPercent()
            ))->toArray();
    }
}
