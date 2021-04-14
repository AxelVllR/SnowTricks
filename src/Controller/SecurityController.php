<?php

namespace App\Controller;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('landing_page');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }


    /**
     * @Route("/token_activation/{token}", name="token_activation")
     */
    public function tokenValidation($token)
    {
        $user = $this->em->getRepository(Users::class)->findOneBy(["token" => $token]);
        if(empty($user)) {
            $this->addFlash("danger", "Compte Introuvable..");
        } else {
            if($user->getIsActivated() !== true) {
                $user->setIsActivated(true);
                $this->em->persist($user);
                $this->em->flush();

                $this->addFlash("success", "Compte activé ! Vous n'avez plus qu'à vous connecter !");
            }
            $this->addFlash("danger", "Ce compte est déjà activé !");
        }

        return $this->redirectToRoute("app_login");
    }


    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


}
