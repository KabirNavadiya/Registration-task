<?php

namespace App\Controller;

use App\Entity\AdminUser;
use App\Entity\Book;
use App\Entity\Loan;
use App\Entity\NormalUser;
use App\Entity\User;
use App\Event\LoanReturnedEvent;
use App\Repository\BookRepository;
use App\Repository\LoanRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage(LoanRepository $loanRepository): Response
    {
        if ($this->getUser()) {
            $user = $this->getUser();
            $userId = $user->getId();
            $loans = $loanRepository->getAllLoansOfCurrentUser($userId);
            foreach ($loans as $loan) {
                if ($loan->getDueAt() < new \DateTime() && $loan->getReturnedAt() === null) {
                    $this->addFlash('warning', 'You have overdue books. Please return them as soon as possible.');
                    break;
                }
            }
            return $this->render('user/homepage.html.twig',[
                'loans' => $loans,
            ]);
        }
        else{
            return $this->render('user/homepage.html.twig');
        }

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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        $book = $bookRepository->find($id);
        if(!$book){
            throw $this->createNotFoundException('Book not found.');
        }
        $loan = new Loan();
        $loan->setUser($user);
        $loan->setBook($book);
        $loan->setLoanedAt(new \DateTimeImmutable());
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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $userid = $user->getId();
        $loans = $loanRepository->getAllLoansOfCurrentUser($userid);

        return $this->render('user/issue_list.html.twig', [
            'loans' => $loans,
        ]);
    }

    /**
     *@Route("/book/return/{id}",name="app_return_book",requirements={"id"="\d+"})
     */

    public function returnBook(int $id,LoanRepository $loanRepository, EntityManagerInterface $entityManager, EventDispatcherInterface $dispatcher): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $loan = $loanRepository->find($id);

        if (!$loan || $loan->getReturnedAt()) {
            throw $this->createNotFoundException('Invalid or already returned loan.');
        }

        $dispatcher->dispatch(new LoanReturnedEvent($loan), LoanReturnedEvent::LOAN_RETURNED);

        $this->addFlash('success', 'Book returned successfully.');
        return $this->redirectToRoute('app_issue_list');
    }
}