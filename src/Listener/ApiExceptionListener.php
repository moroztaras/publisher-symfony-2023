<?php

namespace App\Listener;

use App\Manager\ExceptionHandler\ExceptionMapping;
use App\Manager\ExceptionHandler\ExceptionMappingResolver;
use App\Model\ErrorResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class ApiExceptionListener
{
    public function __construct(
        private ExceptionMappingResolver $resolver,
        private LoggerInterface $logger,
        private SerializerInterface $serializer,
        private bool $isDebug)
    {
    }

    // The event that is called when an exception.
    public function __invoke(ExceptionEvent $event): void
    {
        // Get exception
        $throwable = $event->getThrowable();
        $mapping = $this->resolver->resolve(get_class($throwable));
        if (null === $mapping) {
            $mapping = ExceptionMapping::fromCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Log
        if ($mapping->getCode() >= Response::HTTP_INTERNAL_SERVER_ERROR || $mapping->isLoggable()) {
            $this->logger->error($throwable->getMessage(), [
                'trace' => $throwable->getTraceAsString(),
                'previous' => null !== $throwable->getPrevious() ? $throwable->getPrevious()->getMessage() : '',
            ]);
        }

        // Create message
        $message = $mapping->isHidden() ? Response::$statusTexts[$mapping->getCode()] : $throwable->getMessage();
        // Details for debug mode
        $details = $this->isDebug ? ['trace' => $throwable->getTraceAsString()] : null;
        // Data response
        $data = $this->serializer->serialize(new ErrorResponse($message, $details), JsonEncoder::FORMAT);
        // Assign a response to the client.
        $event->setResponse(new JsonResponse($data, $mapping->getCode(), [], true));
    }
}
