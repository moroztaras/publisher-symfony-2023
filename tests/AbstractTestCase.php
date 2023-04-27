<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Throwable;

abstract class AbstractTestCase extends TestCase
{
    // The method that writes the id to the class entity.
    protected function setEntityId(object $entity, int $value, $idField = 'id')
    {
        // Reflection class
        $class = new \ReflectionClass($entity);
        // Get field id
        $property = $class->getProperty($idField);
        // Open it field
        $property->setAccessible(true);
        // Set value in field id
        $property->setValue($entity, $value);
        // Return to the closed state again
        $property->setAccessible(false);
    }

    // Check response
    protected function assertResponse(int $expectedStatusCode, string $expectedBody, Response $actualResponse): void
    {
        $this->assertEquals($expectedStatusCode, $actualResponse->getStatusCode());
        $this->assertInstanceOf(JsonResponse::class, $actualResponse);
        $this->assertJsonStringEqualsJsonString($expectedBody, $actualResponse->getContent());
    }

    // Creating a test kernel
    protected function createExceptionEvent(Throwable $e): ExceptionEvent
    {
        return new ExceptionEvent(
            $this->createTestKernel(),
            new Request(),
            HttpKernelInterface::MAIN_REQUEST,
            $e
        );
    }

    // Creating a test kernel
    private function createTestKernel(): HttpKernelInterface
    {
        return new class() implements HttpKernelInterface {
            public function handle(Request $request, int $type = self::MAIN_REQUEST, bool $catch = true): Response
            {
                return new Response('test');
            }
        };
    }
}
