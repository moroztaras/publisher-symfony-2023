<?php

namespace App\Tests\Manager;

use App\Exception\CategoryNotFoundException;
use App\Manager\BookManager;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
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
}
