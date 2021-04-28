<?php

namespace App\Controller\Admin;

use App\Entity\TricksPictures;
use App\Entity\Video;
use App\Services\ImageManager;
use Doctrine\ORM\EntityManagerInterface;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class MediaController extends AbstractController {

    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var ImageManager
     */
    private $img;

    public function __construct(EntityManagerInterface $em, ImageManager $img)
    {
        $this->img = $img;
        $this->em = $em;
    }

    /**
     * @Route("/image/delete/{id}", name="image_delete")
     * @IsGranted("ROLE_USER")
     */
    public function deleteImage(int $id) {
        $image = $this->em->getRepository(TricksPictures::class)->findOneBy([
            "id" => $id
        ]);

        if(empty($image)) {
            $this->addFlash("danger", "image introuvable");
            return $this->redirectToRoute("landing_page");
        }

        if($image->getIsPrimary() && $image->getIsPrimary() == true) {
            $this->addFlash("danger", "L'image principale ne peut pas être supprimée");
            return $this->redirectToRoute("trick_edit", ['slug' => $image->getTricks()->getSlug()]);
        }

        $trick = $image->getTricks();

        $image->setTricks(null);
        $this->img->delete($this->getParameter("trick_images_directory") . '/' . $image->getFilename());

        $this->em->remove($image);
        $this->em->flush();

        $this->addFlash("success", "Image supprimée");
        return $this->redirectToRoute("trick_edit", ['slug' => $trick->getSlug()]);
    }
    /**
     * @Route("/video/delete/{id}", name="video_delete")
     * @IsGranted("ROLE_USER")
     */
    public function deleteVideo(int $id) {
        $video = $this->em->getRepository(Video::class)->findOneBy([
            "id" => $id
        ]);

        if(empty($video)) {
            $this->addFlash("danger", "Vidéo introuvable");
            return $this->redirectToRoute("landing_page");
        }

        $trick = $video->getTrick();

        $video->setTrick(null);

        $this->em->remove($video);
        $this->em->flush();
        $this->addFlash("success", "Vidéo supprimée");

        return $this->redirectToRoute("trick_edit", ['slug' => $trick->getSlug()]);
    }

    /**
     * @Route("/image/primary/{id}", name="image_primary")
     * @IsGranted("ROLE_USER")
     */
    public function newPrimaryImage(int $id) {
        $image = $this->em->getRepository(TricksPictures::class)->findOneBy([
            "id" => $id
        ]);

        if(empty($image)) {
            $this->addFlash("danger", "image introuvable");
            return $this->redirectToRoute("landing_page");
        }

        $trick = $image->getTricks();

        foreach($trick->getPictures() as $picture) {
            if(!empty($picture->getIsPrimary()) && $picture->getIsPrimary() == true) {
                $picture->setIsPrimary(null);
                $this->em->persist($picture);
            }
        }

        $image->setIsPrimary(true);
        $this->em->persist($image);

        $this->em->flush();
        return $this->redirectToRoute("trick_edit", ['slug' => $trick->getSlug()]);
    }
}