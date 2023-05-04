<?php

namespace App\Tests\Manager;

use App\Manager\RatingManager;
use App\Repository\ReviewRepository;
use App\Tests\AbstractTestCase;

class RatingManagerTest extends AbstractTestCase
{
    private ReviewRepository $reviewRepository;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock for ReviewRepository
        $this->reviewRepository = $this->createMock(ReviewRepository::class);
    }

    public function provider(): array
    {
        return [
            [25, 20, 1.25],
            [0, 5, 0],
        ];
    }

    /**
     * @dataProvider provider
     */
    public function testCalcReviewRatingForBook(int $repositoryRatingSum, int $total, float $expectedRating): void
    {
        // Behavior and return value for the method
        $this->reviewRepository->expects($this->once())
            ->method('getBookTotalRatingSum')
            ->with(1)
            ->willReturn($repositoryRatingSum);

        // Check matching the expected value from the actual value
        $this->assertEquals(
            $expectedRating,
            (new RatingManager($this->reviewRepository))->calcReviewRatingForBook(1, $total)
        );
    }

    public function testCalcReviewRatingForBookZeroTotal(): void
    {
        // The method - getBookTotalRatingSum is never invoked
        $this->reviewRepository->expects($this->never())->method('getBookTotalRatingSum');

        // Check matching the expected value from the actual value
        $this->assertEquals(
            0,
            (new RatingManager($this->reviewRepository))->calcReviewRatingForBook(1, 0)
        );
    }
}
