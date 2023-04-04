<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    public function __construct(
        private BookRepository $bookRepository,
        private EntityManagerInterface $em,
    ) {
    }

    #[Route('new-book')]
    public function newBook(): Response
    {
        $book = new Book();
        $book->setTitle('Harry Potter');

        $this->em->persist($book);
        $this->em->flush();

        return new Response();
    }

    // Attribute from PHP 8
    #[Route('books')]
    public function listBooks(): Response
    {
        $books = $this->bookRepository->findAll();

        return $this->json(['books' => $books]);
    }
}
