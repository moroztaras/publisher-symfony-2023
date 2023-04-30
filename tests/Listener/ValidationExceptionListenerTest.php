<?php

namespace App\Tests\Listener;

use App\Exception\ValidationException;
use App\Listener\ValidationExceptionListener;
use App\Model\ErrorResponse;
use App\Model\ErrorValidationDetails;
use App\Tests\AbstractTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

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

    public function testInvoke(): void
    {
        // At the output, we want to get an object
        $serialized = json_encode([
            'message' => 'validation failed',
            'details' => [
                'violations' => [
                    ['field' => 'name', 'message' => 'error'],
                ],
            ],
        ]);

        // Create event
        $event = $this->createExceptionEvent(new ValidationException(new ConstraintViolationList([
            new ConstraintViolation('error', null, [], null, 'name', null),
        ])));

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(
                $this->callback(function (ErrorResponse $response) {
                    /** @var ErrorValidationDetails|object $details */
                    $details = $response->getDetails();

                    if (!($details instanceof ErrorValidationDetails)) {
                        return false;
                    }

                    $violations = $details->getViolations();
                    if (1 !== count($violations) || 'validation failed' !== $response->getMessage()) {
                        return false;
                    }

                    return 'name' === $violations[0]->getField() && 'error' === $violations[0]->getMessage();
                }),
                JsonEncoder::FORMAT
            )
            ->willReturn($serialized);

        // Run ValidationExceptionListener
        (new ValidationExceptionListener($this->serializer))($event);

        // Check response
        $this->assertResponse(Response::HTTP_BAD_REQUEST, $serialized, $event->getResponse());
    }
}
