<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\NormalUser as NormalUser;
use App\Entity\CompanyUser as CompanyUser;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRegistrationType extends AbstractType
{

    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {  
        $this->userRepository = $userRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('email')
            ->add('password',PasswordType::class);
        

        $user = $options['data'];
        if($user instanceof CompanyUser){
            $builder
            ->add('companyName')
            ->add('companyEmail');
        }elseif($user instanceof NormalUser){
            $builder->add('isMember',CheckboxType::class);
        }


        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {

            $data = $event->getData();
            $form = $event->getForm();


            if(empty($data->getUsername())){
                $form->get('username')->addError(new FormError('Username cannot be empty!'));
            }
            if(!filter_var($data->getEmail(),FILTER_VALIDATE_EMAIL)){
                $form->get('email')->addError(new FormError('Invalid Email address !'));
            }
            if (method_exists($data, 'getCompanyEmail')) {
                $companyEmail = $data->getCompanyEmail();
                if (!empty($companyEmail) && !filter_var($companyEmail, FILTER_VALIDATE_EMAIL)) {
                    $form->get('companyEmail')->addError(new FormError('Invalid company email address.'));
                }
            }

            $existingUser = $this->userRepository->findOneBy(['email' => $data->getEmail()]);
            if ($existingUser) {
                $form->get('email')->addError(new FormError('This email is already registered.'));
            }

        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'inherit_data' => false,
        ]);
    }
}
