<?php

namespace App\Tests\Mapper;

use App\Entity\Book;
use App\Mapper\BookMapper;
use App\Model\BookDetails;
use App\Tests\AbstractTestCase;

class BookMapperTest extends AbstractTestCase
{
    public function testMap(): void
    {
        // Create entity Book
        $book = (new Book())->setTitle('title')->setSlug('slug')->setImage('123')
            ->setAuthors(['tester'])->setMeap(true)->setPublicationAt(new \DateTimeImmutable('2020-10-10'));

        // Set id to entity Book
        $this->setEntityId($book, 1);

        // Create expected value
        $expected = (new BookDetails())->setId(1)->setSlug('slug')->setTitle('title')->setImage('123')
            ->setAuthors(['tester'])->setMeap(true)->setPublicationAt(1602288000);

        // Check matching the expected value from the actual value
        $this->assertEquals($expected, BookMapper::map($book, new BookDetails()));
    }
}
