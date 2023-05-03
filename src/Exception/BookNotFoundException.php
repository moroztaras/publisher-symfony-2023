<?php

namespace App\Exception;

class BookNotFoundException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Book not found');
    }
}
