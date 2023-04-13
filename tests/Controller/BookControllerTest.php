<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class BookControllerTest extends WebTestCase
{
    public function testBooksByCategory()
    {
        $expectedFile = __DIR__.'/BookControllerTest/testBooksByCategory.json';

        // Create client
        $client = static::createClient();
        // Create request
        $client->request(Request::METHOD_GET, '/api/category/1/books');

        // Get response content
        $responseContent = $client->getResponse()->getContent();

        // Checking the success of the request.
        $this->assertResponseIsSuccessful();

        // Check matching the expected value from the actual value
        $this->assertJsonStringEqualsJsonFile($expectedFile, $responseContent);
    }
}
