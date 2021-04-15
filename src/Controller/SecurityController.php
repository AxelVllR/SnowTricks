<?php

namespace App\Controller;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Services\MailerManager;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SecurityController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em, MailerManager $mailer) {
        $this->em = $em;
        $this->mailer = $mailer;
    }

    public function checkIsAuthenticatedAnonymously() {
        if($this->getUser()) {
            return false;
        }
        return true;
    }

    /**
     * @Route("/password_forgot", name="password_forgot")
     */
    public function passwordForgot(Request $request)
    {
        if(!$this->checkIsAuthenticatedAnonymously()) {
            return $this->redirectToRoute("landing_page");
        }

        $form = $this->createFormBuilder()
            ->add('email', TextType::class, [
                "label" => "Entrez votre E-mail"
            ])->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if(!empty($data['email'])) {
                $user = $this->em->getRepository(Users::class)->findOneBy(['email' => $data['email']]);
                if(!empty($user)) {
                    $token = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
                    $user->setToken($token);
                    $this->em->persist($user);
                    $this->em->flush();
                    $url = $this->getParameter('app.url');
                    $this->mailer->sendPasswordForgot($user, $url);
                    $this->addFlash("success", "Nous venons de vous envoyer un mail, veuillez le consulter");

                } else {
                    $this->addFlash("danger", "Il n'existe aucun compte avec cet Email..");
                }
            } else {
                $this->addFlash("danger", "Veuillez remplir le champ!");
            }
        }

        return $this->render('security/password_forgot.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if(!$this->checkIsAuthenticatedAnonymously()) {
            return $this->redirectToRoute("landing_page");
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
        if(!$this->checkIsAuthenticatedAnonymously()) {
            return $this->redirectToRoute("landing_page");
        }

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
     * @Route("/password_reset/{token}", name="password_reset")
     */
    public function passwordReset($token, Request $request)
    {
        if(!$this->checkIsAuthenticatedAnonymously()) {
            return $this->redirectToRoute("landing_page");
        }

        $user = $this->em->getRepository(Users::class)->findOneBy(["token" => $token]);
        if(empty($user)) {
            $this->addFlash("danger", "Compte Introuvable..");
            return $this->redirectToRoute("password_forgot");
        }

        $form = $this->createFormBuilder()
            ->add('new_pass', PasswordType::class, [
                "label" => "Entrez votre nouveau mot de passe",
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 8]),
                ]
            ])
            ->add('new_pass_conf', PasswordType::class, [
                "label" => "Confirmez votre nouveau mot de passe",
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 8]),
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if(!empty($data['new_pass']) && !empty($data['new_pass_conf'])) {
                $pass = $data['new_pass'];
                $passConf = $data['new_pass_conf'];
                if($pass === $passConf) {
                    $user->setPassword(password_hash($pass, PASSWORD_ARGON2I));
                    $this->em->persist($user);
                    $this->em->flush();
                    $this->addFlash("success", "Mot de passe réinitialisé, vous pouvez maintenant vous authentifier avec celui-ci");
                    return $this->redirectToRoute("app_login");
                } else {
                    $this->addFlash("danger", "Vos deux mots de passe ne correspondent pas..");
                }
            } else {
                $this->addFlash("danger", "Veuillez remplir les champs!");
            }
        }

        return $this->render('security/password_forgot.html.twig', [
            'form' => $form->createView()
        ]);

    }


    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


}
