<?php

namespace App\Controller;

use App\Manager\BookManager;
use App\Model\BookListResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    public function __construct(
        private BookManager $bookManager
    ) {
    }

    // List all books by category
    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns all books by category",
     *
     *     @Model(type=BookListResponse::class)
     * )
     */
    #[Route(path: '/api/category/{id}/books', methods: ['GET'])]
    public function booksByCategory(int $id): Response
    {
        return $this->json($this->bookManager->getBooksByCategory($id));
    }
}
