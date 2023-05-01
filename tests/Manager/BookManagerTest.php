<?php

namespace App\Tests\Manager;

use App\Entity\Book;
use App\Exception\CategoryNotFoundException;
use App\Manager\BookManager;
use App\Model\BookListItem;
use App\Model\BookListResponse;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use App\Repository\ReviewRepository;
use App\Tests\AbstractTestCase;
use Doctrine\Common\Collections\ArrayCollection;

class BookManagerTest extends AbstractTestCase
{
    // Unit tests
    public function testGetBooksByCategoryNotFound(): void
    {
        // Empty mock for ReviewRepository
        $reviewRepository = $this->createMock(ReviewRepository::class);
        // Empty mock for BookRepository
        $bookRepository = $this->createMock(BookRepository::class);
        // Empty mock for CategoryRepository
        $categoryRepository = $this->createMock(CategoryRepository::class);

        // Set behaved method - existsById in CategoryRepository
        $categoryRepository->expects($this->once())
            ->method('existsById')
            ->with(130)
            ->willReturn(false)
        ;
        // We wait exception
        $this->expectException(CategoryNotFoundException::class);

        // We call method - getBooksByCategory
        (new BookManager($bookRepository, $categoryRepository, $reviewRepository))->getBooksByCategory(130);
    }

    public function testGetBooksByCategory(): void
    {
        // Empty mock for ReviewRepository
        $reviewRepository = $this->createMock(ReviewRepository::class);
        // Empty mock for BookRepository
        $bookRepository = $this->createMock(BookRepository::class);
        // Set behaved method - findBooksByCategoryId in BookRepository
        $bookRepository->expects($this->once())
            ->method('findPublishedBooksByCategoryId')
            ->with(130)
            ->willReturn([$this->createBookEntity()]);

        // Empty mock for CategoryRepository
        $categoryRepository = $this->createMock(CategoryRepository::class);
        // Set behaved method - existsById in CategoryRepository
        $categoryRepository->expects($this->once())
            ->method('existsById')
            ->with(130)
            ->willReturn(true);

        $bookManager = new BookManager($bookRepository, $categoryRepository, $reviewRepository);
        // Expected value
        $expected = new BookListResponse([$this->createBookItemModel()]);

        // Check matching the expected value from the actual value
        $this->assertEquals($expected, $bookManager->getBooksByCategory(130));
    }

    private function createBookEntity(): Book
    {
        $book = (new Book())
            ->setTitle('Test Book')
            ->setSlug('test-book')
            ->setMeap(false)
            ->setIsbn('123321')
            ->setDescription('Test description')
            ->setAuthors(['Tester'])
            ->setImage('http://localhost/test.png')
            ->setCategories(new ArrayCollection())
            ->setPublicationAt(new \DateTimeImmutable('2020-10-10'));

        $this->setEntityId($book, 123);

        return $book;
    }

    private function createBookItemModel(): BookListItem
    {
        return (new BookListItem())
            ->setId(123)
            ->setTitle('Test Book')
            ->setSlug('test-book')
            ->setMeap(false)
            ->setAuthors(['Tester'])
//            ->setImage('http://localhost/test.png')
            ->setPublicationAt(1602288000);
    }
}
