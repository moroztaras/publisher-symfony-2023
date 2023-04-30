<?php

namespace App\Tests\Repository;

use App\Entity\Book;
use App\Entity\Category;
use App\Repository\BookRepository;
use App\Tests\AbstractRepositoryTest;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;

class BookRepositoryTest extends AbstractRepositoryTest
{
    private BookRepository $bookRepository;

    protected function setUp(): void
    {
        parent::setUp();
        // G et ref on BookRepository
        $this->bookRepository = $this->getRepositoryForEntity(Book::class);
    }

    public function testFindPublishedBooksByCategoryId()
    {
        // Create category
        $devicesCategory = (new Category())->setTitle('Devices')->setSlug('devices');
        $this->em->persist($devicesCategory);

        // Create new books
        for ($i = 0; $i < 5; ++$i) {
            $book = $this->createBook('device-'.$i, $devicesCategory);
            $this->em->persist($book);
        }

        $this->em->flush();

        $this->assertCount(5, $this->bookRepository->findPublishedBooksByCategoryId($devicesCategory->getId()));
    }

    // F-n for create new book
    private function createBook(string $title, Category $category): Book
    {
        return (new Book())
            ->setPublicationAt(new DateTimeImmutable())
            ->setAuthors(['author'])
            ->setMeap(false)
            ->setSlug($title)
            ->setCategories(new ArrayCollection([$category]))
            ->setTitle($title)
            ->setImage('http://localhost/'.$title.'.png')
        ;
    }
}
