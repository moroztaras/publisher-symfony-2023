<?php

namespace App\Tests\Manager\ExceptionHandler;

use App\Manager\ExceptionHandler\ExceptionMappingResolver;
use App\Tests\AbstractTestCase;

class ExceptionMappingResolverTest extends AbstractTestCase
{
    // Test for no code
    public function testThrowsExceptionOnEmptyCode(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        // We do not transfer a code that is mandatory
        new ExceptionMappingResolver(['someClass' => ['hidden' => true]]);
    }

    public function testResolvesToNullWhenNotFound(): void
    {
        $resolver = new ExceptionMappingResolver([]);
        // If no class is found in the settings, we'll return null.
        $this->assertNull($resolver->resolve(\InvalidArgumentException::class));
    }

    // We can find a class that directly corresponds to what we specified
    public function testResolvesClassItself(): void
    {
        $resolver = new ExceptionMappingResolver([\InvalidArgumentException::class => ['code' => 400]]);
        $mapping = $resolver->resolve(\InvalidArgumentException::class);

        $this->assertEquals(400, $mapping->getCode());
        $this->assertTrue($mapping->isHidden());
        $this->assertFalse($mapping->isLoggable());
    }

    // We can find a  subclass that directly matches the one we specified
    public function testResolvesSubClass(): void
    {
        $resolver = new ExceptionMappingResolver([\LogicException::class => ['code' => 500]]);
        $mapping = $resolver->resolve(\InvalidArgumentException::class);

        $this->assertEquals(500, $mapping->getCode());
    }

    // Checking what hidden is specified.
    public function testResolvesHidden(): void
    {
        $resolver = new ExceptionMappingResolver([\LogicException::class => ['code' => 500, 'hidden' => false]]);
        $mapping = $resolver->resolve(\LogicException::class);

        $this->assertFalse($mapping->isHidden());
    }

    // Checking what loggable is specified.
    public function testResolvesLoggable(): void
    {
        $resolver = new ExceptionMappingResolver([\LogicException::class => ['code' => 500, 'loggable' => true]]);
        $mapping = $resolver->resolve(\LogicException::class);

        $this->assertTrue($mapping->isLoggable());
    }
}
