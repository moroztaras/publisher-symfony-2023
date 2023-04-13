<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

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
}
