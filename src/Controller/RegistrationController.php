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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
class RegistrationController extends AbstractController
{

    private UserPasswordHasherInterface $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /** 
     * @Route("/register/{type}",name="app_register", requirements = {"slug"="normalUser|companyUser"})
     */
    public function register(string $type, Request $request, EntityManagerInterface $entityManager) : Response
    {
        if($type == "normalUser"){
            $user = new NormalUser();
        }   
        else if($type == "companyUser"){
            $user = new CompanyUser();
        }

        // dd($user);
        $form = $this->createForm(UserRegistrationType::class,$user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $submittedUser = $form->getData();
            $plainPassword = $form->get('password')->getData();
            $hashedPassword = $this->passwordHasher->hashPassword($submittedUser, $plainPassword);
            $submittedUser->setPassword($hashedPassword);
        
            $entityManager->persist($submittedUser);
            $entityManager->flush();
            // return $this->redirectToRoute('app_dashboard', [
            //     'type' => $type,
            //     'email' => $submittedUser->getEmail(),
            // ]);
            return $this->redirectToRoute('app_login');
            
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
            'slug' => $type,
        ]);
    }


    /**
     * 
     * @Route("/dashboard/{type}/{email}",name="app_dashboard")
     */
    public function registrationSuccess(string $type,string $email, EntityManagerInterface $em):Response
    {
        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            throw $this->createNotFoundException("User not found.");
        }
    
        return $this->render('dashboard/dashboard.html.twig', [
            'slug' => $type,
            'user' => $user,
            
        ]);
    } 



}