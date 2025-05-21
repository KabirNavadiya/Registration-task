<?php

namespace App\Controller;

use App\Entity\AdminUser;
use App\Entity\Book;
use App\Entity\Loan;
use App\Entity\NormalUser;
use App\Entity\User;
use App\Repository\BookRepository;
use App\Repository\LoanRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage()
    {
        return $this->render('user/homepage.html.twig');
    }

    /**
     * @Route("/data",name="app_available_books")
     */
    public function getAllAvailableBooks(BookRepository $bookRepository): JsonResponse
    {
        $books = $bookRepository->findAllAvailableBooks();
        $data = [];

        foreach ($books as $book) {
            $data[] = [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'author' => $book->getAuthor(),
                'actions' => [
                    $this->generateUrl('app_issue_book', ['id' => $book->getId()]),
                ],
            ];
        }

        return $this->json(['data' => $data]);

    }

    /**
     * @Route("/issuebook/{id}",name="app_issue_book",requirements={"id"="\d+"})
     */
    public function issueBook(int $id,EntityManagerInterface $entityManager,BookRepository $bookRepository):Response
    {
        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute('app_login');
        }
        $book = $bookRepository->find($id);
        if(!$book){
            throw $this->createNotFoundException('Book not found.');
        }
        $loan = new Loan();
        $loan->setUser($user);
        $loan->setBook($book);
        $loan->setLoanedAt(new \DateTimeImmutable());
        $loan->setDueAt(new \DateTimeImmutable('+14 days'));
        $book->setIsAvailable(false);

        $entityManager->persist($loan);
        $entityManager->flush();
        $this->addFlash('success', 'Book issued successfully!');

        return $this->redirectToRoute('app_homepage');
    }


    /**
     * @Route("/issue-list",name="app_issue_list")
     */
    public function viewIssueList(LoanRepository $loanRepository): Response
    {
        $loans = $loanRepository->findBy(['returnedAt' => null]);

        return $this->render('user/issue_list.html.twig', [
            'loans' => $loans,
        ]);
    }
    /**
     * @Route("/issue-list/data",name="app_issue_list_data")
     */
    public function getIssuedBooks(LoanRepository $loanRepository): JsonResponse
    {

        $data=[];
        foreach ($loans as $loan) {
            $book = $loan->getBook();
            $data[] = [
                'title' => $book->getTitle(),
                'author' => $book->getAuthor(),
                'loanedAt' => $loan->getLoanedAt()->format('Y-m-d'),
                'dueAt' => $loan->getDueAt()->format('Y-m-d'),
                'actions' => [
                    $this->generateUrl('app_return_book', ['id' => $loan->getId()])
                ]
            ];
        }
        return $this->json(['data' => $data]);
    }

    /**
     *@Route("/book/return/{id}",name="app_return_book",requirements={"id"="\d+"})
     */

    public function returnBook(int $id,LoanRepository $loanRepository, EntityManagerInterface $entityManager ): Response
    {
        $loan = $loanRepository->find($id);

        if (!$loan || $loan->getReturnedAt()) {
            throw $this->createNotFoundException('Invalid or already returned loan.');
        }

        $loan->setReturnedAt(new \DateTimeImmutable());
        $loan->getBook()->setIsAvailable(true);

        $entityManager->flush();

        $this->addFlash('success', 'Book returned successfully.');
        return $this->redirectToRoute('app_issue_list');
    }


}