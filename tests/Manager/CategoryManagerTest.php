<?php

declare(strict_types=1);

namespace App\Tests\Manager;

use App\Entity\Category;
use App\Manager\CategoryManager;
use App\Model\CategoryListItem;
use App\Model\CategoryListResponse;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\Criteria;
use PHPUnit\Framework\TestCase;

class CategoryManagerTest extends TestCase
{
    // Unit test
    public function testGetCategories(): void
    {
        // Mock depend CategoryRepository
        $repository = $this->createMock(CategoryRepository::class);

        // Set the behavior to method findBy
        $repository->expects($this->once())
            ->method('findBy')
            ->with([], ['title' => Criteria::ASC])
            ->willReturn([(new Category())->setId(7)->setTitle('Test')->setSlug('test')]); // return value

        // Real instance
        $categoryManager = new CategoryManager($repository);

        // Expected value
        $expected = new CategoryListResponse([new CategoryListItem(7, 'Test', 'test')]);

        // Check matching the expected value from the actual value
        $this->assertEquals($expected, $categoryManager->getCategories());
    }
}
