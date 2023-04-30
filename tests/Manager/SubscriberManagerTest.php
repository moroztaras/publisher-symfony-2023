<?php

namespace App\Tests\Manager;

use App\Entity\Subscriber;
use App\Exception\SubscriberAlreadyExistsException;
use App\Manager\SubscriberManager;
use App\Model\SubscriberRequest;
use App\Repository\SubscriberRepository;
use App\Tests\AbstractTestCase;
use Doctrine\ORM\EntityManagerInterface;

class SubscriberManagerTest extends AbstractTestCase
{
    private SubscriberRepository $repository;

    private EntityManagerInterface $em;

    private const EMAIL = 'test@test.com';

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->createMock(SubscriberRepository::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
    }

    // Test on already exists email
    public function testSubscribeAlreadyExists(): void
    {
        $this->expectException(SubscriberAlreadyExistsException::class);

        // Setting the returned value
        $this->repository->expects($this->once())
            ->method('existsByEmail')
            ->with(self::EMAIL)
            ->willReturn(true);

        // Create request
        $request = new SubscriberRequest();
        $request->setEmail(self::EMAIL);

        // Create manager
        (new SubscriberManager($this->repository, $this->em))->subscribe($request);
    }

    // Test on successful data saving in the database
    public function testSubscribe(): void
    {
        // Setting the returned value for existsByEmail
        $this->repository->expects($this->once())
            ->method('existsByEmail')
            ->with(self::EMAIL)
            ->willReturn(false);

        $expectedSubscriber = new Subscriber();
        $expectedSubscriber->setEmail(self::EMAIL);

        // Setting mock for persist
        $this->em->expects($this->once())
            ->method('persist')
            ->with($expectedSubscriber);

        // Setting mock for flush
        $this->em->expects($this->once())
            ->method('flush');

        // Create Request
        $request = new SubscriberRequest();
        $request->setEmail(self::EMAIL);

        (new SubscriberManager($this->repository, $this->em))->subscribe($request);
    }
}
