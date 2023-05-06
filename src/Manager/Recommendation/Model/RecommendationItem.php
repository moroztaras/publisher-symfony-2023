<?php

namespace App\Manager\Recommendation\Model;

class RecommendationItem
{
    public function __construct(private int $id)
    {
    }

    public function getId(): int
    {
        return $this->id;
    }
}
