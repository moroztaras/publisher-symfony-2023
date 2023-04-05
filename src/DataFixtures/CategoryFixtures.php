<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    final public const ANDROID_CATEGORY = 'android';
    final public const DEVICES_CATEGORY = 'devices';

    public function load(ObjectManager $manager): void
    {
        $categories = [
            self::DEVICES_CATEGORY => (new Category())->setTitle('Devices')->setSlug('devices'),
            self::ANDROID_CATEGORY => (new Category())->setTitle('Android')->setSlug('android'),
        ];

        foreach ($categories as $category) {
            $manager->persist($category);
        }

        $manager->persist((new Category())->setTitle('Networking')->setSlug('networking'));

        $manager->flush();

        foreach ($categories as $code => $category) {
            $this->addReference($code, $category);
        }
    }
}
