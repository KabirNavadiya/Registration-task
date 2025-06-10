<?php

namespace App\Form;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class BookType extends AbstractType
{
    private $bookRepository;
    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {


        $builder
            ->add('title')
            ->add('author')
            ->add('imageFile',FileType::class,[
                'mapped'=>false,
                'required'=>false,
                'constraints' => [
                    new Image(
                        ['maxSize' => '3m'],
                    )
                ]
            ])
            ->add('isAvailable')
        ;

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            $existingBooks = $this->bookRepository->findOneBy(['title'=> $data->getTitle()]);
            if ($existingBooks) {
                $form->get('title')->addError(new FormError('This book is already available.'));
            }
        });


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
