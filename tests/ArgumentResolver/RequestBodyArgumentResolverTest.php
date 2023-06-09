<?php

namespace App\Tests\ArgumentResolver;

use App\ArgumentResolver\RequestBodyArgumentResolver;
use App\Attribute\RequestBody;
use App\Exception\RequestBodyConvertException;
use App\Exception\ValidationException;
use App\Tests\AbstractTestCase;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestBodyArgumentResolverTest extends AbstractTestCase
{
    private SerializerInterface $serializer;

    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
    }

    // Tests for a missing attribute
    public function testNotSupports(): void
    {
        $meta = new ArgumentMetadata('some', null, false, false, null);

        $this->assertEmpty($this->createResolver()->resolve(new Request(), $meta));
    }

    // When an exception occurs, we should catch it and redirect it to our exception RequestBodyConvertException
    public function testResolveThrowsWhenDeserialize(): void
    {
        // We wait Exception from RequestBodyConvertException
        $this->expectException(RequestBodyConvertException::class);

        // Create request
        $request = new Request([], [], [], [], [], [], 'testing content');

        // Create meta
        $meta = new ArgumentMetadata('some', \stdClass::class, false, false, null, false, [
            new RequestBody(),
        ]);

        // Setting deserialize
        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->with('testing content', \stdClass::class, JsonEncoder::FORMAT)
            ->willThrowException(new \Exception());

        // Create resolve
        $this->createResolver()->resolve($request, $meta);
    }

    // Check Validation return not empty and trigger ValidationException
    public function testResolveThrowsWhenValidationFails(): void
    {
        // We wait Exception from ValidationException
        $this->expectException(ValidationException::class);

        // Let's mark and encode the body
        $body = ['test' => true];
        $encodedBody = json_encode($body);

        // Create request
        $request = new Request([], [], [], [], [], [], $encodedBody);
        // Create meta
        $meta = new ArgumentMetadata('some', \stdClass::class, false, false, null, false, [
            new RequestBody(),
        ]);

        // Setting deserialize
        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->with($encodedBody, \stdClass::class, JsonEncoder::FORMAT)
            ->willReturn($body);

        // Setting validate
        $this->validator->expects($this->once())
            ->method('validate')
            ->with($body)
            ->willReturn(new ConstraintViolationList([
                new ConstraintViolation('error', null, [], null, 'some', null),
            ]));

        // Create resolve
        $this->createResolver()->resolve($request, $meta);
    }

    public function testResolve(): void
    {
        $body = ['test' => true];
        $encodedBody = json_encode($body);

        $request = new Request([], [], [], [], [], [], $encodedBody);
        $meta = new ArgumentMetadata('some', \stdClass::class, false, false, null, false, [
            new RequestBody(),
        ]);

        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->with($encodedBody, \stdClass::class, JsonEncoder::FORMAT)
            ->willReturn($body);

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($body)
            ->willReturn(new ConstraintViolationList([]));

        $actual = $this->createResolver()->resolve($request, $meta);

        // The body is compared to what the generator will return
        $this->assertEquals($body, $actual->current());
    }

    private function createResolver(): RequestBodyArgumentResolver
    {
        return new RequestBodyArgumentResolver($this->serializer, $this->validator);
    }
}
