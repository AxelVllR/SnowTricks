<?php

namespace App\Services;

use App\Entity\TricksPictures;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ImageManager extends AbstractController{

    const EXTENSIONS_ACCEPTED = [
        "png",
        "jpeg",
        "jpg"
    ];

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function setTrickImage($imagePost, TricksPictures $image) {
        if(!$this->imageSecurity($imagePost)) {
            return false;
        }

        // Get Trick Entity
        $trick = $image->getTricks();
        
        $filename = md5(uniqid()) . '.' . $imagePost->guessExtension();
        // copy image in directory
        $imagePost->move(
            $this->getParameter("trick_images_directory"),
            $filename
        );

        if(count($trick->getPictures()) === 0) {
            $image->setIsPrimary(true);
        }

        $image->setFilename($filename);
        $this->em->persist($image);
        $this->em->flush();
        return true;
    }

    public function setUserImage($imagePost, Users $user) {
        if(!$this->imageSecurity($imagePost)) {
            return false;
        }

        if(!empty($user->getFilename()) && file_exists($this->getParameter("user_images_directory") . '/' . $user->getFilename())) {
            unlink($this->getParameter("user_images_directory") . '/' . $user->getFilename());
        }

        $filename = md5(uniqid()) . '.' . $imagePost->guessExtension();
        // copy image in directory
        $imagePost->move(
            $this->getParameter("user_images_directory"),
            $filename
        );

        $user->setFilename($filename);
        $this->em->persist($user);
        return true;
    }

    public function delete($path) {
        if(file_exists($path)) {
            unlink($path);
        }
    }

    public function imageSecurity($imagePost): bool
    {
        $extension = $imagePost->guessExtension();
        if(in_array($extension, self::EXTENSIONS_ACCEPTED)) {
            return true;
        }
        return false;
    }

}