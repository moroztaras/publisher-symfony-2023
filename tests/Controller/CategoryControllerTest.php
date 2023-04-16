<?php

namespace App\Tests\Controller;

use App\Entity\Category;
use App\Tests\AbstractControllerTest;
use Symfony\Component\HttpFoundation\Request;

class CategoryControllerTest extends AbstractControllerTest
{
    // Functional test
    public function testCategories(): void
    {
        // Create new category in test DB
        $this->em->persist((new Category())->setTitle('Devices')->setSlug('devices'));
        $this->em->flush();
        // Create request
        $this->client->request(Request::METHOD_GET, '/api/categories');

        // Convert response content to array
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        // Checking the success of the request.
        $this->assertResponseIsSuccessful();

        // Check matching the actual value with the expected schema
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['items'],
            'properties' => [
                'items' => [
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
}
