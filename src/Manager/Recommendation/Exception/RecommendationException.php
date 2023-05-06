<?php

namespace App\Manager\Recommendation\Exception;

class RecommendationException extends \Exception
{
    public function __construct(string $message = '', ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
