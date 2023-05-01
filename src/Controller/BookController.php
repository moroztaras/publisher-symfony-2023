<?php

namespace App\Controller;

use App\Manager\BookManager;
use App\Model\BookDetails;
use App\Model\BookListResponse;
use App\Model\ErrorResponse;
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
     *     description="Returns books inside category",
     *
     *     @Model(type=BookListResponse::class)
     * )
     *
     * @OA\Response(
     *     response=404,
     *     description="book category not found",
     *
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(path: '/api/category/{id}/books', methods: ['GET'])]
    public function booksByCategory(int $id): Response
    {
        return $this->json($this->bookManager->getBooksByCategory($id));
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns book detail information",
     *
     *     @Model(type=BookDetails::class)
     * )
     *
     * @OA\Response(
     *     response=404,
     *     description="Book not found",
     *
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(path: '/api/book/{id}', methods: ['GET'])]
    public function bookById(int $id): Response
    {
        return $this->json($this->bookManager->getBookById($id));
    }
}
