<?php

namespace App\Controller;

use App\Manager\ReviewManager;
use App\Model\ReviewPage;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReviewController extends AbstractController
{
    public function __construct(private ReviewManager $reviewManager)
    {
    }

    /**
     * @OA\Parameter(name="page", in="query", description="Page number", @OA\Schema(type="integer"))
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns page of reviews for the given book",
     *
     *     @Model(type=ReviewPage::class)
     * )
     */
    #[Route(path: '/api/book/{id}/reviews', methods: ['GET'])]
    public function reviews(int $id, Request $request): Response
    {
        return $this->json($this->reviewManager->getReviewPageByBookId(
            $id, $request->query->get('page', 1)
        ));
    }
}
