<?php

namespace App\Tests\Listener;

use App\Listener\ApiExceptionListener;
use App\Manager\ExceptionHandler\ExceptionMapping;
use App\Manager\ExceptionHandler\ExceptionMappingResolver;
use App\Model\ErrorDebugDetails;
use App\Model\ErrorResponse;
use App\Tests\AbstractTestCase;
use Doctrine\Instantiator\Exception\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class ApiExceptionListenerTest extends AbstractTestCase
{
    private ExceptionMappingResolver $resolver;

    private LoggerInterface $logger;

    private SerializerInterface $serializer;

    // Run before every test
    protected function setUp(): void
    {
        parent::setUp();

        // M ock of dependencies
        $this->resolver = $this->createMock(ExceptionMappingResolver::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->serializer = $this->createMock(SerializerInterface::class);
    }

    public function testNon500MappingWithHiddenMessage(): void
    {
        $mapping = ExceptionMapping::fromCode(Response::HTTP_NOT_FOUND);
        // Pending messages and body that we will receive in error response
        $responseMessage = Response::$statusTexts[$mapping->getCode()];
        $responseBody = json_encode(['error' => $responseMessage]);

        // The returned value from the method resolve.
        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(\InvalidArgumentException::class)
            ->willReturn($mapping);

        // The returned value from the method serialize.
        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($responseMessage), JsonEncoder::FORMAT)
            ->willReturn($responseBody);

        // Create event
        $event = $this->createExceptionEvent(new \InvalidArgumentException('test'));

        // Run event listener
        $this->runListener($event);

        // Check response
        $this->assertResponse(Response::HTTP_NOT_FOUND, $responseBody, $event->getResponse());
    }

    //  Get a message from an exception, not a pre-defined one.
    public function testNon500MappingWithPublicMessage(): void
    {
        $mapping = new ExceptionMapping(Response::HTTP_NOT_FOUND, false, false);
        // Pending messages and body that we will receive in error response
        $responseMessage = 'Test response message';
        $responseBody = json_encode(['error' => $responseMessage]);

        // Set return value for method of resolve
        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(\InvalidArgumentException::class)
            ->willReturn($mapping);

        // Set return value for method of serialize
        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($responseMessage), JsonEncoder::FORMAT)
            ->willReturn($responseBody);

        // Create event
        $event = $this->createExceptionEvent(new \InvalidArgumentException($responseMessage));

        // Run event listener
        $this->runListener($event);

        // Check response
        $this->assertResponse(Response::HTTP_NOT_FOUND, $responseBody, $event->getResponse());
    }

    // Checking that the logger actually triggered.
    public function testNon500LoggableMappingTriggersLogger(): void
    {
        $mapping = new ExceptionMapping(Response::HTTP_NOT_FOUND, false, true);
        // Pending messages and body that we will receive in error response
        $responseMessage = 'test';
        $responseBody = json_encode(['error' => $responseMessage]);

        // Set return value for method of resolve
        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(InvalidArgumentException::class)
            ->willReturn($mapping);

        // Set return value for method of serialize
        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($responseMessage), JsonEncoder::FORMAT)
            ->willReturn($responseBody);

        // Logger settings
        $this->logger->expects($this->once())
            ->method('error');

        // Create event
        $event = $this->createExceptionEvent(new InvalidArgumentException($responseMessage));

        // Run event listener
        $this->runListener($event);

        // Check response
        $this->assertResponse(Response::HTTP_NOT_FOUND, $responseBody, $event->getResponse());
    }

    // Checking that the logger triggered when response code is 500.
    public function test500IsLoggable(): void
    {
        $mapping = ExceptionMapping::fromCode(Response::HTTP_GATEWAY_TIMEOUT);
        // Pending messages and body that we will receive in error response
        $responseMessage = Response::$statusTexts[$mapping->getCode()];
        $responseBody = json_encode(['error' => $responseMessage]);

        // Set return value for method of resolve
        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(InvalidArgumentException::class)
            ->willReturn($mapping);

        // Set return value for method of serialize
        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($responseMessage), JsonEncoder::FORMAT)
            ->willReturn($responseBody);

        // Logger settings with arguments
        $this->logger->expects($this->once())
            ->method('error')
            ->with('error message', $this->anything());

        // Create event
        $event = $this->createExceptionEvent(new InvalidArgumentException('error message'));

        // Run event listener
        $this->runListener($event);

        // Check response
        $this->assertResponse(Response::HTTP_GATEWAY_TIMEOUT, $responseBody, $event->getResponse());
    }

    // When the method 'resolve' return null, then we have to give 500.
    public function test500IsDefaultWhenMappingNotFound(): void
    {
        // Pending messages and body that we will receive in error response
        $responseMessage = Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR];
        $responseBody = json_encode(['error' => $responseMessage]);

        // Set return value for method of resolve
        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(InvalidArgumentException::class)
            ->willReturn(null);

        // Set return value for method of serialize
        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($responseMessage), JsonEncoder::FORMAT)
            ->willReturn($responseBody);

        // Create event
        $this->logger->expects($this->once())
            ->method('error')
            ->with('error message', $this->anything());

        // Create event
        $event = $this->createExceptionEvent(new InvalidArgumentException('error message'));

        // Run event listener
        $this->runListener($event);

        // Check response
        $this->assertResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $responseBody, $event->getResponse());
    }

    // When the debug mode, we show the trace
    public function testShowTraceWhenDebug(): void
    {
        $mapping = ExceptionMapping::fromCode(Response::HTTP_NOT_FOUND);
        // Pending messages and body that we will receive in error response
        $responseMessage = Response::$statusTexts[$mapping->getCode()];
        $responseBody = json_encode(['error' => $responseMessage, 'trace' => 'something']);

        // Set return value for method of resolve
        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(InvalidArgumentException::class)
            ->willReturn($mapping);

        // Set return value for method of serialize
        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(
                $this->callback(function (ErrorResponse $response) use ($responseMessage) {
                    /** @var ErrorDebugDetails|object $details */
                    $details = $response->getDetails();

                    return $response->getMessage() == $responseMessage &&
                        $details instanceof ErrorDebugDetails && !empty($details->getTrace());
                }),
                JsonEncoder::FORMAT
            )
            ->willReturn($responseBody);

        // Create event
        $event = $this->createExceptionEvent(new InvalidArgumentException('error message'));

        // Run event listener in debug mode
        $this->runListener($event, true);

        // Check response
        $this->assertResponse(Response::HTTP_NOT_FOUND, $responseBody, $event->getResponse());
    }

    // Construct and run the listener
    private function runListener(ExceptionEvent $event, bool $isDebug = false): void
    {
        (new ApiExceptionListener($this->resolver, $this->logger, $this->serializer, $isDebug))($event);
    }
}
