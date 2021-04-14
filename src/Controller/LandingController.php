<?php

namespace App\Controller;

use App\Entity\Groups;
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
    public function landingController(?int $page, Request $request): Response
    {
        $groups = $this->em->getRepository(Groups::class)->findAll();

        $page = 1;

        if($get = $request->query->get('page')) {
            $page = $get;
        }

        $group = null;
        if($grp = $request->query->get('group')) {
            $group = $grp;
        }

        $nbOfTricks = $this->em->getRepository(Tricks::class)->countAll($group);

        $pages = ceil($nbOfTricks / 10);

        $first = ($page * 10) - 10;

        $tricks = $this->em->getRepository(Tricks::class)->findByPages($first, 10, $group);

        $options = [
            "tricks" => $tricks,
            "pages" => $pages,
            "current" => $page,
            "groups" => $groups,
            "total" => $nbOfTricks
        ];

        if(isset($group) && !empty($group)) {
            $options["group"] = $group;
        }

        return $this->render('landing_page.html.twig', $options);
    }
}