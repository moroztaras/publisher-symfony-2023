<?php

namespace App\Tests\Controller;

use App\Entity\Book;
use App\Entity\Category;
use App\Tests\AbstractControllerTest;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;

class BookControllerTest extends AbstractControllerTest
{
    public function testBooksByCategory()
    {
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
}
