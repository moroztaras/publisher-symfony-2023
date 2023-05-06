<?php

namespace App\Manager\Recommendation;

use App\Manager\Recommendation\Exception\AccessDeniedException;
use App\Manager\Recommendation\Exception\RequestException;
use App\Manager\Recommendation\Model\RecommendationResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RecommendationManager
{
    public function __construct(
        private HttpClientInterface $recommendationClient,
        private SerializerInterface $serializer
    ) {
    }

    // Get recommendations by book
    /**
     * @throws AccessDeniedException
     * @throws RequestException
     */
    public function getRecommendationsByBookId(int $bookId): RecommendationResponse
    {
        try {
            $response = $this->recommendationClient->request(Request::METHOD_GET, '/api/book/'.$bookId.'/recommendations');

            return $this->serializer->deserialize(
                $response->getContent(),
                RecommendationResponse::class,
                JsonEncoder::FORMAT
            );
        } catch (\Throwable $ex) {
            if ($ex instanceof TransportExceptionInterface && Response::HTTP_FORBIDDEN === $ex->getCode()) {
                throw new AccessDeniedException($ex);
            }

            throw new RequestException($ex->getMessage(), $ex);
        }
    }
}
