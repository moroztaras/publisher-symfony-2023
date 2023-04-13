<?php

namespace App\Tests\Manager;

use App\Entity\Book;
use App\Entity\Category;
use App\Exception\CategoryNotFoundException;
use App\Manager\BookManager;
use App\Model\BookListItem;
use App\Model\BookListResponse;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class BookManagerTest extends TestCase
{
    private BookRepository $bookRepository;
    private CategoryRepository $categoryRepository;

    protected function setUp(): void
    {
        parent::setUp();
        // Empty mock without behavior
        $this->bookRepository = $this->createMock(BookRepository::class);
        $this->categoryRepository = $this->createMock(CategoryRepository::class);
    }

    public function testGetBooksByCategoryNotFound(): void
    {
        // Set behaved method - existsById in categoryRepository
        $this->categoryRepository->expects($this->once())
            ->method('existsById')
            ->with(130)
            ->willReturn(false);

        $this->expectException(CategoryNotFoundException::class);

        // We call method - getBooksByCategory
        (new BookManager($this->bookRepository, $this->categoryRepository))->getBooksByCategory(130);
    }

    public function testGetBooksByCategory(): void
    {
        // Mock for bookRepository & categoryRepository

        // Set behaved method - findPublishedBooksByCategoryId in bookRepository
        $this->bookRepository->expects($this->once())
            ->method('findPublishedBooksByCategoryId')
            ->with(130)
            ->willReturn([$this->createBookEntity()]);

        // Set behaved method - find in categoryRepository
        $this->categoryRepository->expects($this->once())
            ->method('find')
            ->with(130)
            ->willReturn(new Category());

        // Create book manager
        $bookManager = new BookManager($this->bookRepository, $this->categoryRepository);
        // Expected value
        $expected = new BookListResponse([$this->createBookItemModel()]);

        $this->assertEquals($expected, $bookManager->getBooksByCategory(130));
    }

    private function createBookEntity(): Book
    {
        return (new Book())
            ->setId(123)
            ->setTitle('Test book')
            ->setSlug('test-book')
            ->setMeap(false)
            ->setAuthors(['Tester'])
            ->setImage('http://localhost/test.png')
            ->setCategories(new ArrayCollection())
            ->setPublicationAt(new \DateTime('2020-10-10'))
        ;
    }

    private function createBookItemModel(): BookListItem
    {
        return (new BookListItem())
            ->setId(123)
            ->setTitle('Test book')
            ->setSlug('test-book')
            ->setMeap(false)
            ->setAuthors(['Tester'])
            ->setImage('http://localhost/test.png')
            ->setPublicationAt(1602288000)
        ;
    }
}
