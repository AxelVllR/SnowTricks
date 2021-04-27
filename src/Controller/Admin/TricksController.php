<?php
namespace App\Controller\Admin;

use App\Entity\Comments;
use App\Entity\Tricks;
use App\Entity\TricksPictures;
use App\Entity\Video;
use App\Form\CommentType;
use App\Form\TricksPictureType;
use App\Form\TrickType;
use App\Form\VideoType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class TricksController
 * @package App\Controller\Admin
 */
class TricksController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }


    /**
     * @Route("/edit/{slug}", name="trick_edit")
     * @IsGranted("ROLE_USER")
     */
    public function editTrick(string $slug, Request $request) {
        $trick = $this->em->getRepository(Tricks::class)->findOneBy(["slug" => $slug]);

        if(!isset($trick) && empty($trick)) {
            $this->addFlash("danger", "Trick introuvable");
            return $this->redirectToRoute("landing_page");
        }

        $video = (new Video())->setTrick($trick);
        $videoForm = $this->createForm(VideoType::class, $video);
        $videoForm->handleRequest($request);


        if($videoForm->isSubmitted() && $videoForm->isValid()) {
            $this->em->persist($video);
            $this->em->flush();

            $this->addFlash("success", "Vidéo Ajoutée !");

            return $this->redirectToRoute("trick_edit", ["slug" => $trick->getSlug()]);
        }


        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $trick->setUpdatedBy($this->getUser());
            $trick->setUpdatedAt(new \DateTime('now'));
            $this->em->flush();

            $this->addFlash("success", "Trick Modifié");

            return $this->redirectToRoute("landing_page");
        }

        $image = (new TricksPictures())->setTricks($trick);
        $imageForm = $this->createForm(TricksPictureType::class, $image);
        $imageForm->handleRequest($request);

        if($imageForm->isSubmitted() && $imageForm->isValid()) {
            $imagePost = $imageForm->get('image')->getData();
            // generate filename
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

            $this->addFlash("success", "Image ajoutée !");
            return $this->redirectToRoute("trick_edit", ["slug" => $trick->getSlug()]);
        }

        return $this->render('admin/trick_edit.html.twig', [
            'form' => $form->createView(),
            'btn' => "Modifier",
            'trick' => $trick,
            'video_form' => $videoForm->createView(),
            'image_form' => $imageForm->createView()
        ]);
    }

    /**
     * @Route("/add", name="trick_add")
     * @IsGranted("ROLE_USER")
     */
    public function addTrick(Request $request) {
        $trick = new Tricks();
        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $trick->setCreatedAt(new \DateTime('now'));
            $trick->setCreatedBy($this->getUser());
            $this->em->persist($trick);
            $this->em->flush();

            $this->addFlash("success", "Trick Ajouté");

            return $this->redirectToRoute("trick_edit", ["slug" => $trick->getSlug()]);
        }

        return $this->render('admin/trick_edit.html.twig', [
            'form' => $form->createView(),
            'btn' => "Ajouter"
        ]);
    }


    /**
     * @Route("/delete/{slug}", name="trick_delete", methods="DELETE")
     * @IsGranted("ROLE_USER")
     */
    public function deleteTrick(string $slug, Request $request) {

        $trick = $this->em->getRepository(Tricks::class)->findOneBy(["slug" => $slug]);
        if(!isset($trick) && empty($trick)) {
            $this->addFlash("danger", "Ce Trick n'existe pas !");
            return $this->redirectToRoute("landing_page");
        }

        if($this->isCsrfTokenValid('delete' . $trick->getId(), $request->get('_token'))) {
            foreach($trick->getPictures() as $picture) {
                $this->em->remove($picture);
            }

            foreach($trick->getVideos() as $video) {
                $this->em->remove($video);
            }
            $this->em->remove($trick);
            $this->em->flush();
            $this->addFlash("success", "Trick Supprimé !");
        } else {
            $this->addFlash("danger", "Token CSRF non valide !");
        }
        return $this->redirectToRoute("landing_page");
    }

    /**
     * @Route("/trick/{slug}", name="trick_see")
     */
    public function trickSee(string $slug, Request $request) {
        $params = [];

        $page = 1;
        if($get = $request->get('page')) {
            $page = $get;
        }

        $trick = $this->em->getRepository(Tricks::class)->findOneBy(["slug" => $slug]);

        $params['trick'] = $trick;

        if(!isset($trick) && empty($trick)) {
            $this->addFlash("danger", "Ce Trick n'existe pas !");
            return $this->redirectToRoute("landing_page");
        }

        $primary_image = $this->em->getRepository(TricksPictures::class)->findOneBy([
            "tricks" => $trick,
            "is_primary" => true
        ]);

        $params['primaryImage'] = $primary_image;

        $nbOfComments = $this->em->getRepository(Comments::class)->countAll($trick);
        $pages = ceil($nbOfComments / 10);
        $first = ($page * 10) - 10;

        $comments = $this->em->getRepository(Comments::class)->findByLimit($trick, $first, 10);

        $params['current'] = $page;
        $params['pages'] = $pages;
        $params["comments"] = $comments;

        if($this->getUser()) {
            $comment = (new Comments())
                ->setUser($this->getUser())
                ->setTrick($trick);
            $form = $this->createForm(CommentType::class, $comment);

            $params['form'] = $form->createView();

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                $comment->setCreatedAt(new \DateTime('now'));
                $this->em->persist($comment);
                $this->em->flush();

                $this->addFlash("success", "Commentaire Ajouté");

                return $this->redirectToRoute("trick_see", ["slug" => $trick->getSlug()]);
            }
        }

        return $this->render("trick/trick_page.html.twig", $params);

    }

}