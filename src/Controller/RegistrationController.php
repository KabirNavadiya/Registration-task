<?php

namespace App\Controller;

use App\Entity\AdminUser;
use App\Entity\NormalUser;
use App\Entity\User;
use App\Form\UserRegistrationType;
use App\Message\Command\LogEmoji;
use App\Message\WelcomeUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
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
    public function register(string $type, Request $request, EntityManagerInterface $entityManager, MessageBusInterface $messageBus) : Response
    {
        if($type == "normalUser"){
            $user = new NormalUser();
        }   
        else if($type == "adminUser"){
            $user = new AdminUser();
            $user->setRoles(["ROLE_LIBRARIAN"]);

        }

        $form = $this->createForm(UserRegistrationType::class,$user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $submittedUser = $form->getData();
            $plainPassword = $form->get('password')->getData();
            $hashedPassword = $this->passwordHasher->hashPassword($submittedUser, $plainPassword);
            $submittedUser->setPassword($hashedPassword);
            $entityManager->persist($submittedUser);
            $entityManager->flush();


            $message = new WelcomeUser($submittedUser);
            $envelope = new Envelope($message,[
                new DelayStamp(5000),
            ]);
            $messageBus->dispatch($envelope);
            $messageBus->dispatch(new LogEmoji(1));

            return $this->redirectToRoute('app_login');
            
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
            'slug' => $type,
        ]);
    }

}