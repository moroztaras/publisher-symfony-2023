<?php

namespace App\Tests\Controller;

use App\Tests\AbstractControllerTest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SubscribeControllerTest extends AbstractControllerTest
{
    public function testSubscribe(): void
    {
        $content = json_encode(['email' => 'test@test.com', 'agreed' => true]);
        $this->client->request('POST', '/api/subscribe', [], [], [], $content);

        $this->assertResponseIsSuccessful();
    }
}
