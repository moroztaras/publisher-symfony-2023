<?php

namespace App\Tests\Manager;

use App\Entity\Book;
use App\Exception\CategoryNotFoundException;
use App\Manager\BookManager;
use App\Manager\RatingManager;
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
    private ReviewRepository $reviewRepository;

    private BookRepository $bookRepository;

    private CategoryRepository $categoryRepository;

    private RatingManager $ratingManager;

    protected function setUp(): void
    {
        parent::setUp();
        // Empty mock for repositories and manager
        $this->reviewRepository = $this->createMock(ReviewRepository::class);
        $this->bookRepository = $this->createMock(BookRepository::class);
        $this->categoryRepository = $this->createMock(CategoryRepository::class);
        $this->ratingManager = $this->createMock(RatingManager::class);
    }

    public function testGetBooksByCategoryNotFound(): void
    {
        // Set behaved method - existsById in CategoryRepository
        $this->categoryRepository->expects($this->once())
            ->method('existsById')
            ->with(130)
            ->willReturn(false)
        ;
        // We wait exception
        $this->expectException(CategoryNotFoundException::class);

        // We call method - getBooksByCategory
        $this->createBookManager()->getBooksByCategory(130);
    }

    public function testGetBooksByCategory(): void
    {
        // Set behaved method - findPublishedBooksByCategoryId in BookRepository
        $this->bookRepository->expects($this->once())
            ->method('findPublishedBooksByCategoryId')
            ->with(130)
            ->willReturn([$this->createBookEntity()]);

        // Set behaved method - existsById in CategoryRepository
        $this->categoryRepository->expects($this->once())
            ->method('existsById')
            ->with(130)
            ->willReturn(true);

        // Expected value
        $expected = new BookListResponse([$this->createBookItemModel()]);

        // Check matching the expected value from the actual value
        $this->assertEquals($expected, $this->createBookManager()->getBooksByCategory(130));
    }

    // Create BookManager
    private function createBookManager(): BookManager
    {
        return new BookManager(
            $this->bookRepository,
            $this->categoryRepository,
            $this->reviewRepository,
            $this->ratingManager
        );
    }

    // Create entity book
    private function createBookEntity(): Book
    {
        $book = (new Book())
            ->setTitle('Test Book')
            ->setSlug('test-book')
            ->setMeap(false)
            ->setIsbn('123321')
            ->setDescription('test description')
            ->setAuthors(['Tester'])
            ->setImage('http://localhost/test.png')
            ->setCategories(new ArrayCollection())
            ->setPublicationAt(new \DateTimeImmutable('2020-10-10'));

        $this->setEntityId($book, 123);

        return $book;
    }

    // Create Book Item Model
    private function createBookItemModel(): BookListItem
    {
        return (new BookListItem())
            ->setId(123)
            ->setTitle('Test Book')
            ->setSlug('test-book')
            ->setMeap(false)
            ->setAuthors(['Tester'])
            ->setImage('http://localhost/test.png')
            ->setPublicationAt(1602288000);
    }
}
