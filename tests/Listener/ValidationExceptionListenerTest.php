<?php

namespace App\Tests\Listener;

use App\Listener\ValidationExceptionListener;
use App\Tests\AbstractTestCase;
use Symfony\Component\Serializer\SerializerInterface;

class ValidationExceptionListenerTest extends AbstractTestCase
{
    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->serializer = $this->createMock(SerializerInterface::class);
    }

    public function testInvokeSkippedWhenNotValidationException(): void
    {
        $this->serializer->expects($this->never())
            ->method('serialize');

        // Create event with Exception
        $event = $this->createExceptionEvent(new \Exception());

        // Run ValidationExceptionListener
        (new ValidationExceptionListener($this->serializer))($event);
    }


}
