<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Repository\LoanRepository;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/books", name="app_manage_books")
     */
    public function adminpage(BookRepository $bookRepository):Response
    {
        $this->denyAccessUnlessGranted('ROLE_LIBRARIAN');
        return $this->render('admin/managebooks.html.twig');
    }

    /**
     * @Route("/admin/books/data", name="app_manage_books_data")
     */
    public function getBooksAjax(BookRepository $bookRepository): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_LIBRARIAN');

        $books = $bookRepository->findAll();
        $data = [];

        foreach ($books as $book) {
            $data[] = [
                'id' => $book->getId(),
                'image' =>$book->getImageFilename(),
                'title' => $book->getTitle(),
                'author' => $book->getAuthor(),
                'isAvailable' => $book->getIsAvailable() ? 'Yes' : 'No',
                'actions' => [
                    $this->generateUrl('app_edit_book', ['id' => $book->getId()]),
                    $this->generateUrl('app_delete_book', ['id' => $book->getId()])
                    ],
            ];
        }

        return $this->json(['data' => $data]);
    }

    /**
    * @Route("/admin/loans/{page<\d+>}",name="app_view_loans")
    */
    public function viewLoans(LoanRepository $loanRepository,Request $request, int $page = 1) : Response
    {
        $this->denyAccessUnlessGranted('ROLE_LIBRARIAN');
        $queryBuilder = $loanRepository->getAllLoans();
        $pagerfanta = new Pagerfanta(
            new QueryAdapter($queryBuilder)
        );

        $pagerfanta->setMaxPerPage(10);
        $pagerfanta->setCurrentPage($page);

        return $this->render('admin/loans.html.twig',[
            'loans' => $pagerfanta,
        ]);
    }




}