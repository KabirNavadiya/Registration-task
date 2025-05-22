<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    /**
     * @Route("/admin/books/new",name="app_add_book")
    */
    public function addBook(Request $request, EntityManagerInterface $entityManager):Response
    {
        $this->denyAccessUnlessGranted('ROLE_LIBRARIAN');

        $book = new Book();
        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($book);
            $entityManager->flush();

            $this->addFlash('success', 'Book added successfully!');
            return $this->redirectToRoute('app_manage_books');
        }

        return $this->render('admin/addbook.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/books/edit/{id}", name="app_edit_book",requirements={"id"="\d+"})
     */
    public function editBook(int $id, Request $request, EntityManagerInterface $entityManager):Response
    {
        $this->denyAccessUnlessGranted('ROLE_LIBRARIAN');

        $book = $entityManager->getRepository(Book::class)->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Book not found.');
        }

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($book);
            $entityManager->flush();
            $this->addFlash('success', 'Book Updated successfully!');
            return $this->redirectToRoute('app_manage_books');
        }


        return $this->render('admin/editbook.html.twig', [
            'form' => $form->createView(),
            'book' => $book,
        ]);
    }

    /**
     * @Route("/admin/books/delete/{id}", name="app_delete_book",requirements={"id"="\d+"})
     */
    public function deleteBook( int $id, EntityManagerInterface $entityManager):Response
    {
        $this->denyAccessUnlessGranted('ROLE_LIBRARIAN');
        $book = $entityManager->getRepository(Book::class)->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Book not found.');
        }
        $entityManager->remove($book);
        $entityManager->flush();

        $this->addFlash('success', 'Book deleted successfully!');
        return $this->redirectToRoute('app_manage_books');    }



}