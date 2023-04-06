<?php

namespace App\Controller;

use App\Manager\CategoryManager;
use App\Model\CategoryListResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    public function __construct(
        private CategoryManager $categoryManager
    ) {
    }

    // List all categories
    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns book categories",
     *
     *     @Model(type=CategoryListResponse::class)
     * )
     */
    #[Route(path: '/api/book/categories', methods: ['GET'])]
     public function categories(): Response
     {
         return $this->json($this->categoryManager->getCategories());
     }
}
