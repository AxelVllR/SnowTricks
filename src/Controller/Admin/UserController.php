<?php
namespace App\Controller\Admin;

use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class UserController
 * @package App\Controller\Admin
 * @IsGranted("ROLE_USER")
 */
class UserController extends AbstractController {

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    /**
     * @Route("/profil", name="profile")
     */
    public function profile(Request $request)
    {
        $user =$this->getUser();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash("success", "Profil Modifié");

            return $this->redirectToRoute("profile");
        }

        return $this->render('admin/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}