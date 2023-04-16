<?php

namespace App\Tests\Repository;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Tests\AbstractRepositoryTest;

class CategoryRepositoryTest extends AbstractRepositoryTest
{
    private CategoryRepository $bookCategoryRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookCategoryRepository = $this->getRepositoryForEntity(Category::class);
    }

    public function testFindAllSortedByTitle()
    {
        // Create three categories
        $devices = (new Category())->setTitle('Devices')->setSlug('devices');
        $android = (new Category())->setTitle('Android')->setSlug('android');
        $computer = (new Category())->setTitle('Computer')->setSlug('computer');
        // Persist
        foreach ([$devices, $android, $computer] as $category) {
            $this->em->persist($category);
        }
        // Flash
        $this->em->flush();

        // We get only title
        $titles = array_map(
            fn (Category $bookCategory) => $bookCategory->getTitle(),
            $this->bookCategoryRepository->findAllSortedByTitle(),
        );

        // Expected value
        $expected = ['Android', 'Computer', 'Devices'];

        // Check matching the expected value from the actual value
        $this->assertEquals($expected, $titles);
    }
}
