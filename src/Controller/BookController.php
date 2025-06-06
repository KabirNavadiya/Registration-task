<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    /**
     * @Route("/admin/books/new",name="app_add_book")
    */
    public function addBook(Request $request, EntityManagerInterface $entityManager,UploaderHelper $uploaderHelper):Response
    {
        $this->denyAccessUnlessGranted('ROLE_LIBRARIAN');

        $book = new Book();
        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile  $uploadedFile*/
            $uploadedFile = $form->get('imageFile')->getData();

            $newFilename = $uploaderHelper->uploadBookImage($uploadedFile);
            $book->setImageFilename($newFilename);

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
    public function editBook(int $id, Request $request, EntityManagerInterface $entityManager, UploaderHelper $uploaderHelper):Response
    {
        $this->denyAccessUnlessGranted('ROLE_LIBRARIAN');

        $book = $entityManager->getRepository(Book::class)->find($id);

        if (!$book) {
            throw $this->createNotFoundException('Book not found.');
        }
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile  $uploadedFile*/
            $uploadedFile = $form->get('imageFile')->getData();

            if($uploadedFile) {
                $newFilename = $uploaderHelper->uploadBookImage($uploadedFile);
                $book->setImageFilename($newFilename);
            }
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