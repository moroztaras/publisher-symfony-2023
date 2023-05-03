<?php

declare(strict_types=1);

namespace App\Tests\Manager;

use App\Entity\Category;
use App\Manager\CategoryManager;
use App\Model\Category as CategoryModel;
use App\Model\CategoryListResponse;
use App\Repository\CategoryRepository;
use App\Tests\AbstractTestCase;

class CategoryManagerTest extends AbstractTestCase
{
    // Unit test
    public function testGetCategories(): void
    {
        // Create category
        $category = (new Category())->setTitle('Test')->setSlug('test');

        // Set id for category
        $this->setEntityId($category, 7);

        // Mock depend on CategoryRepository
        $repository = $this->createMock(CategoryRepository::class);

        // Set the behavior to method findBy
        $repository->expects($this->once())
            ->method('findAllSortedByTitle')
            ->willReturn([$category]); // return category

        // Real instance
        $categoryManager = new CategoryManager($repository);

        // Expected value
        $expected = new CategoryListResponse([new CategoryModel(7, 'Test', 'test')]);

        // Check matching the expected value from the actual value
        $this->assertEquals($expected, $categoryManager->getCategories());
    }
}
