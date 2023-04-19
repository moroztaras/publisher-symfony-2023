<?php

namespace App\Manager\ExceptionHandler;

class ExceptionMappingResolver
{
    /**
     * @var ExceptionMapping[]
     */
    private array $mappings = [];

    public function __construct(array $mappings)
    {
        // Check valid input data
        foreach ($mappings as $class => $mapping) {
            // Check input code
            if (empty($mapping['code'])) {
                throw new \InvalidArgumentException('Code is mandatory for class'.$class);
            }

            $this->addMapping(
                $class,
                $mapping['code'],
                $mapping['hidden'] ?? true,
                $mapping['loggable'] ?? false
            );
        }
    }

    // Accepts the exception class that was released
    public function resolve(string $throwableClass): ?ExceptionMapping
    {
        $foundMapping = null;

        foreach ($this->mappings as $class => $mapping) {
            // Mapping wants to find a class or subclass that directly corresponds.
            if ($throwableClass === $class || is_subclass_of($throwableClass, $class)) {
                $foundMapping = $mapping;
                break;
            }
        }

        return $foundMapping;
    }

    private function addMapping(string $class, int $code, bool $hidden, bool $loggable): void
    {
        // Create instance ExceptionMapping
        $this->mappings[$class] = new ExceptionMapping($code, $hidden, $loggable);
    }
}
