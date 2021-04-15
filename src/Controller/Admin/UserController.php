<?php
namespace App\Controller\Admin;

use App\Entity\Users;
use App\Form\PasswordUpdateType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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

    /**
     * @Route("/profil/update-password", name="update_password")
     */
    public function updatePassword(Request $request)
    {
        $user =$this->getUser();

        $form = $this->createFormBuilder()
            ->add('old_password', PasswordType::class, [
                "label" => "Ancien mot de passe"
            ])
            ->add('new_password', PasswordType::class, [
                "label" => "Nouveau mot de passe"
            ])
            ->add('new_password_conf', PasswordType::class, [
                "label" => "Confirmation de votre nouveau mot de passe"
            ])
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            if(!empty($formData['old_password']) && !empty($formData['new_password']) && !empty($formData['new_password_conf'])) {
                $old = $formData['old_password'];
                $new = $formData['new_password'];
                $newConf = $formData['new_password_conf'];

                if($new == $newConf && password_verify($old, $user->getPassword())) {
                    if(strlen($new) > 8) {
                        $sessUser = $this->em->getRepository(Users::class)->findOneBy(['id' => $user->getId()]);
                        $sessUser->setPassword(password_hash($new, PASSWORD_ARGON2I));
                        $this->em->persist($sessUser);
                        $this->em->flush();
                        $this->addFlash("success", "Mot de passe modifié");
                        return $this->redirectToRoute("update_password");
                    }
                    $this->addFlash("danger", "Attention, votre mot de passe doit contenir au moins 8 caractères !");
                    return $this->redirectToRoute("update_password");

                }
                $this->addFlash("danger", "Attention, il y a une erreur dans un de vos champs");
                return $this->redirectToRoute("update_password");
            }

            $this->addFlash("danger", "Attention, il y a une erreur dans un de vos champs");

            return $this->redirectToRoute("update_password");
        }

        return $this->render('admin/profile.html.twig', [
            'form' => $form->createView(),
            "type" => "update_password"
        ]);
    }
}