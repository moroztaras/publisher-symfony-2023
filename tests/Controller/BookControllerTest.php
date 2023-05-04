<?php

namespace App\Tests\Controller;

use App\Entity\Book;
use App\Entity\BookFormat;
use App\Entity\BookToBookFormat;
use App\Entity\Category;
use App\Tests\AbstractControllerTest;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;

class BookControllerTest extends AbstractControllerTest
{
    public function testBooksByCategory()
    {
        // Get category id
        $categoryId = $this->createCategory();

        // Create request
        $this->client->request(Request::METHOD_GET, '/api/category/'.$categoryId.'/books');

        // Get response content
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        // Checking the success of the request.
        $this->assertResponseIsSuccessful();

        // Check matching the expected value from the actual value
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['items'],
            'properties' => [
                'items' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['id', 'title', 'slug', /* 'image', */ 'authors', 'meap', 'publicationAt'],
                        'properties' => [
                            'title' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'id' => ['type' => 'integer'],
                            'publicationAt' => ['type' => 'integer'],
                            'image' => ['type' => 'string'],
                            'meap' => ['type' => 'boolean'],
                            'authors' => [
                                'type' => 'array',
                                'items' => ['type' => 'string'],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testBookById(): void
    {
        // Get Book od
        $bookId = $this->createBook();

        $this->client->request(Request::METHOD_GET, '/api/book/'.$bookId);
        // Get response content
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        // Checking the success of the request.
        $this->assertResponseIsSuccessful();

        // Check matching the expected value from the actual value
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => [
                'id', 'title', 'slug', 'image', 'authors', 'meap', 'publicationAt', 'rating', 'reviews',
                'categories', 'formats',
            ],
            'properties' => [
                'title' => ['type' => 'string'],
                'slug' => ['type' => 'string'],
                'id' => ['type' => 'integer'],
                'publicationAt ' => ['type' => 'integer'],
                'image' => ['type' => 'string'],
                'meap' => ['type' => 'boolean'],
                'authors' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                ],
                'rating' => ['type' => 'number'],
                'reviews' => ['type' => 'integer'],
                'categories' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['id', 'title', 'slug'],
                        'properties' => [
                            'title' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'id' => ['type' => 'integer'],
                        ],
                    ],
                ],
            ],
        ]);
    }

    private function createCategory(): int
    {
        // Create category
        $category = (new Category())->setTitle('Devices')->setSlug('devices');
        // Persist category
        $this->em->persist($category);

        $this->em->persist((new Book())
            ->setTitle('Test book')
            ->setImage('http://localhost.png')
            ->setMeap(true)
            ->setIsbn('123321')
            ->setDescription('test')
            ->setPublicationAt(new \DateTimeImmutable())
            ->setAuthors(['Tester'])
            ->setCategories(new ArrayCollection([$category]))
            ->setSlug('test-book')
        );

        $this->em->flush();

        return $category->getId();
    }

    private function createBook(): int
    {
        // Create category
        $category = (new Category())->setTitle('Devices')->setSlug('devices');
        $this->em->persist($category);

        // Create format
        $format = (new BookFormat())
            ->setTitle('format')
            ->setDescription('description format')
            ->setComment(null);

        $this->em->persist($format);

        // Create book
        $book = (new Book())
            ->setTitle('Test book')
            ->setImage('http://localhost.png')
            ->setMeap(true)
            ->setIsbn('123321')
            ->setDescription('test')
            ->setPublicationAt(new \DateTimeImmutable())
            ->setAuthors(['Tester'])
            ->setCategories(new ArrayCollection([$category]))
            ->setSlug('test-book');

        $this->em->persist($book);

        // Create join to format
        $join = (new BookToBookFormat())->setPrice(123.55)
            ->setFormat($format)
            ->setDiscountPercent(5)
            ->setBook($book);

        $this->em->persist($join);

        $this->em->flush();

        return $book->getId();
    }
}
