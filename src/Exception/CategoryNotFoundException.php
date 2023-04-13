<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class CategoryNotFoundException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Book category not found', Response::HTTP_NOT_FOUND);
    }
}
