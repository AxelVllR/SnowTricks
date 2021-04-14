<?php
namespace App\Controller\Admin;

use App\Entity\Comments;
use App\Entity\Tricks;
use App\Entity\Video;
use App\Form\CommentType;
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
     * @Route("/edit/{id}", name="trick_edit")
     * @IsGranted("ROLE_USER")
     */
    public function editTrick(int $id, Request $request) {
        $trick = $this->em->getRepository(Tricks::class)->findOneBy(["id" => $id]);


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

            return $this->redirectToRoute("trick_edit", ["id" => $trick->getId()]);
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

        return $this->render('admin/trick_edit.html.twig', [
            'form' => $form->createView(),
            'btn' => "Modifier",
            'trick' => $trick,
            'video_form' => $videoForm->createView()
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

            return $this->redirectToRoute("landing_page");
        }

        return $this->render('admin/trick_edit.html.twig', [
            'form' => $form->createView(),
            'btn' => "Ajouter"
        ]);
    }


    /**
     * @Route("/delete/{id}", name="trick_delete", methods="DELETE")
     * @IsGranted("ROLE_USER")
     */
    public function deleteTrick(int $id, Request $request) {

        $trick = $this->em->getRepository(Tricks::class)->findOneBy(["id" => $id]);
        if(!isset($trick) && empty($trick)) {
            $this->addFlash("danger", "Ce Trick n'existe pas !");
            return $this->redirectToRoute("landing_page");
        }

        if($this->isCsrfTokenValid('delete' . $trick->getId(), $request->get('_token'))) {
            foreach($trick->getPictures() as $picture) {
                $this->em->remove($picture);
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
     * @Route("/trick/{id}", name="trick_see")
     */
    public function trickSee(int $id, Request $request) {
        $params = [];

        $page = 1;
        if($get = $request->get('page')) {
            $page = $get;
        }

        $trick = $this->em->getRepository(Tricks::class)->findOneBy(["id" => $id]);

        $params['trick'] = $trick;

        if(!isset($trick) && empty($trick)) {
            $this->addFlash("danger", "Ce Trick n'existe pas !");
            return $this->redirectToRoute("landing_page");
        }


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

                return $this->redirectToRoute("trick_see", ["id" => $trick->getId()]);
            }
        }

        return $this->render("trick/trick_page.html.twig", $params);

    }

}