<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class CategoryControllerTest extends WebTestCase
{
    // Functional test
    public function testCategories(): void
    {
        $expectedFile = __DIR__.'/CategoryControllerTest/testCategories.json';

        // Create client
        $client = static::createClient();
        // Create request
        $client->request(Request::METHOD_GET, '/api/categories');

        // Get response content
        $responseContent = $client->getResponse()->getContent();

        // Checking the success of the request.
        $this->assertResponseIsSuccessful();

        // Check matching the expected value from the actual value
        $this->assertJsonStringEqualsJsonFile($expectedFile, $responseContent);
    }
}
