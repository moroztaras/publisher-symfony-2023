<?php

namespace App\Mapper;

use App\Entity\Book;
use App\Model\BookDetails;
use App\Model\BookListItem;
use App\Model\RecommendedBook;

// Mapper for book
class BookMapper
{
    public static function map(Book $book, BookDetails|BookListItem $model): BookDetails|BookListItem
    {
        return $model
            ->setId($book->getId())
            ->setTitle($book->getTitle())
            ->setSlug($book->getSlug())
            ->setImage($book->getImage())
            ->setAuthors($book->getAuthors())
            ->setMeap($book->isMeap())
            ->setPublicationAt($book->getPublicationAt()->getTimestamp());
    }

    public static function mapRecommended(Book $book): RecommendedBook
    {
        $description = $book->getDescription();
        $description = strlen($description) > 150 ? substr($description, 0, 150).'...' : $description;

        // Set map
        return (new RecommendedBook())
            ->setId($book->getId())
            ->setImage($book->getImage())
            ->setSlug($book->getSlug())
            ->setTitle($book->getTitle())
            ->setShortDescription($description);
    }
}
