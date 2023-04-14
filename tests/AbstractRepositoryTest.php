<?php

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class AbstractRepositoryTest extends KernelTestCase
{
    protected ?EntityManagerInterface $em;

    // The method is run before each test.
    protected function setUp(): void
    {
        parent::setUp();

        $this->em = self::getContainer()->get('doctrine.orm.entity_manager');
    }

    protected function getRepositoryForEntity(string $entityClass): mixed
    {
        return $this->em->getRepository($entityClass);
    }

    // The method is run, after run each test.
    protected function tearDown(): void
    {
        parent::tearDown();

        // Close entity manager
        $this->em->close();
        // Set value null for entity manager
        $this->em = null;
    }
}
