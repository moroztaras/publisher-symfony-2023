<?php

namespace App\Manager;

use App\Entity\Subscriber;
use App\Exception\SubscriberAlreadyExistsException;
use App\Model\SubscriberRequest;
use App\Repository\SubscriberRepository;
use Doctrine\ORM\EntityManagerInterface;

class SubscriberManager
{
    public function __construct(
        private SubscriberRepository $subscriberRepository,
        private EntityManagerInterface $em
    ) {
    }

    public function subscribe(SubscriberRequest $request): void
    {
        // Check email
        if ($this->subscriberRepository->existsByEmail($request->getEmail())) {
            throw new SubscriberAlreadyExistsException();
        }

        // New subscriber
        $subscriber = (new Subscriber())->setEmail($request->getEmail());

        $this->em->persist($subscriber);
        $this->em->flush();
    }
}
