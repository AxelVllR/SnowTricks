<?php

namespace App\Controller;

use App\Entity\Tricks;
use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Security\UsersAuthenticator;
use ContainerMpsRnxZ\EntityManager_9a5be93;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class LandingController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @Route("/", name="landing_page")
     */
    public function landingController(): Response
    {
        $tricks = $this->em->getRepository(Tricks::class)->findAll();

        return $this->render('landing_page.html.twig', [
            "tricks" => $tricks
        ]);
    }
}