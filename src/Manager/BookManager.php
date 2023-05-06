<?php

namespace App\Manager;

use App\Entity\Book;
use App\Entity\BookToBookFormat;
use App\Entity\Category;
use App\Exception\CategoryNotFoundException;
use App\Manager\Recommendation\Model\RecommendationItem;
use App\Manager\Recommendation\RecommendationManager;
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
use Psr\Log\LoggerInterface;

class BookManager
{
    public function __construct(
        private BookRepository $bookRepository,
        private CategoryRepository $categoryRepository,
        private ReviewRepository $reviewRepository,
        private RatingManager $ratingManager,
        private RecommendationManager $recommendationManager,
        private LoggerInterface $logger)
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
        $recommendations = [];

        // Remaps data from the database to the model
        $categories = $book->getCategories()
            ->map(fn (Category $bookCategory) => new BookCategoryModel(
                $bookCategory->getId(), $bookCategory->getTitle(), $bookCategory->getSlug()
            ));

        try {
            $recommendations = $this->getRecommendations($id);
        } catch (\Exception $ex) {
            $this->logger->error('Error while fetching recommendations', [
                'exception' => $ex->getMessage(),
                'bookId' => $id,
            ]);
        }

        return BookMapper::map($book, new BookDetails())
            ->setRating($this->ratingManager->calcReviewRatingForBook($id, $reviews))
            ->setReviews($reviews)
            ->setRecommendations($recommendations)
            ->setFormats($this->mapFormats($book->getFormats()))
            ->setCategories($categories->toArray());
    }

    private function getRecommendations(int $bookId): array
    {
        $ids = array_map(
            fn (RecommendationItem $item) => $item->getId(),
            $this->recommendationManager->getRecommendationsByBookId($bookId)->getRecommendations()
        );

        return array_map([BookMapper::class, 'mapRecommended'], $this->bookRepository->findBooksByIds($ids));
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
