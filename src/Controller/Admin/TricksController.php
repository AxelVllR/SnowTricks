<?php
namespace App\Controller\Admin;

use App\Entity\Comments;
use App\Entity\Tricks;
use App\Form\CommentType;
use App\Form\TrickType;
use Doctrine\ORM\EntityManagerInterface;
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
            'trick' => $trick
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
        $trick = $this->em->getRepository(Tricks::class)->findOneBy(["id" => $id]);

        $params['trick'] = $trick;

        if(!isset($trick) && empty($trick)) {
            $this->addFlash("danger", "Ce Trick n'existe pas !");
            return $this->redirectToRoute("landing_page");
        }

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