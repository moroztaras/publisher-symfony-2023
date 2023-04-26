<?php

namespace App\Tests\ArgumentResolver;

use App\ArgumentResolver\RequestBodyArgumentResolver;
use App\Attribute\RequestBody;
use App\Exception\RequestBodyConvertException;
use App\Exception\ValidationException;
use App\Tests\AbstractTestCase;
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

    private function createResolver(): RequestBodyArgumentResolver
    {
        return new RequestBodyArgumentResolver($this->serializer, $this->validator);
    }
}
