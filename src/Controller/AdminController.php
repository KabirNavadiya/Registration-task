<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/books", name="app_manage_books")
     */
    public function adminpage()
    {
        $this->denyAccessUnlessGranted('ROLE_LIBRARIAN');
        return $this->render('admin/managebooks.html.twig');
    }

    /**
    * @Route("/admin/loans",name="app_view_loans")
    */
    public function viewLoans()
    {
        $this->denyAccessUnlessGranted('ROLE_LIBRARIAN');
        return $this->render('admin/loans.html.twig');
    }

}