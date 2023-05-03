<?php

namespace App\Manager;

use App\Entity\Review;
use App\Model\Review as ReviewModel;
use App\Model\ReviewPage;
use App\Repository\ReviewRepository;

class ReviewManager
{
    private const PAGE_LIMIT = 5;

    public function __construct(
        private ReviewRepository $reviewRepository,
        private RatingManager $ratingManager
    ) {
    }

    public function getReviewPageByBookId(int $id, int $page): ReviewPage
    {
        // Calculate offset
        $offset = max($page - 1, 0) * self::PAGE_LIMIT;
        $paginator = $this->reviewRepository->getPageByBookId($id, $offset, self::PAGE_LIMIT);
        // Total number of pages
        $total = count($paginator);
        $items = [];

        foreach ($paginator as $item) {
            $items[] = $this->map($item);
        }

        // Response
        return (new ReviewPage())
            ->setRating($this->ratingManager->calcReviewRatingForBook($id, $total))
            ->setTotal($total)
            ->setPage($page)
            ->setPerPage(self::PAGE_LIMIT)
            ->setPages(ceil($total / self::PAGE_LIMIT))
            ->setItems($items)
        ;
    }

    public function map(Review $review): ReviewModel
    {
        return (new ReviewModel())
            ->setId($review->getId())
            ->setRating($review->getRating())
            ->setCreatedAt($review->getCreatedAt()->getTimestamp())
            ->setAuthor($review->getAuthor())
            ->setContent($review->getContent());
    }
}
