<?php

namespace App\Controller;

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
        return $this->render('homepage.html.twig');
    }
    /**
     * @Route("/admin", name="app_admin")
     */
    public function adminpage()
    {
        $this->denyAccessUnlessGranted('ROLE_LIBRARIAN');
        return $this->render('admin/admin.html.twig');
    }

    
}