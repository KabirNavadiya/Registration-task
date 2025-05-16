<?php

namespace App\Controller;

use App\Entity\CompanyUser;
use App\Entity\NormalUser;
use App\Entity\User;
use App\Form\UserRegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class RegistrationController extends AbstractController
{

    /** 
     * @Route("/register/{slug}",name="app_register")
     */
    public function register(string $slug, Request $request, EntityManagerInterface $entityManager) : Response
    {
        if($slug == "normalUser"){
            $user = new NormalUser();
        }   
        else if($slug == "companyUser"){
            $user = new CompanyUser();
        }

        // dd($user);
        $form = $this->createForm(UserRegistrationType::class,$user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $submittedUser = $form->getData();
            $entityManager->persist($submittedUser);
            $entityManager->flush();
            return $this->redirectToRoute('app_dashboard', [
                'slug' => $slug,
                'email' => $submittedUser->getEmail(),
            ]);
            
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
            'slug' => $slug,
        ]);
    }


    /**
     * 
     * @Route("/dashboard/{slug}/{email}",name="app_dashboard")
     */
    public function registrationSuccess(string $slug,string $email, EntityManagerInterface $em):Response
    {
        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            throw $this->createNotFoundException("User not found.");
        }
    
        return $this->render('dashboard/dashboard.html.twig', [
            'slug' => $slug,
            'user' => $user,
            
        ]);
    } 



}