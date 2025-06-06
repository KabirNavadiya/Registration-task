<?php

namespace App\Service;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    private string $uploadPaths;
    private EntityManagerInterface $entityManager;

    public function __construct(string $uploadPaths,EntityManagerInterface $entityManager)
    {

        $this->uploadPaths = $uploadPaths;
        $this->entityManager = $entityManager;
    }
    public function uploadBookImage(UploadedFile $uploadedFile): string
    {
        $destination = $this->uploadPaths . '/books';
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $newFilename = Urlizer::urlize($originalFilename) . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

        $uploadedFile->move($destination, $newFilename);
        return $newFilename;
    }
}