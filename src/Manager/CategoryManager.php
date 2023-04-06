<?php

namespace App\Manager;

use App\Entity\Category;
use App\Model\CategoryListItem;
use App\Model\CategoryListResponse;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\Criteria;

class CategoryManager
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository
    ) {
    }

    public function getCategories(): CategoryListResponse
    {
        // Get all categories from repository by title
        $categories = $this->categoryRepository->findBy([], ['title' => Criteria::ASC]);

        // The list of entities will map to the list of models.
        $items = array_map(
            fn (Category $category) => new CategoryListItem(
                $category->getId(), $category->getTitle(), $category->getSlug()
            ),
            $categories
        );

        return new CategoryListResponse($items);
    }
}
