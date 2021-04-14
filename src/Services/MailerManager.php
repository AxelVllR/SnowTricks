<?php

namespace App\Services;

use App\Entity\Users;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Dotenv\Dotenv;


class MailerManager {
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(\Swift_Mailer $mailer, ContainerInterface $container) {
        $this->mailer = $mailer;
        $this->container = $container;
    }

    public function sendMailAccountCreated(Users $user, $url) {
        $mail = $user->getEmail();
        $obj = $user->getPseudo() . '! Bienvenue chez SnowTricks !';

        $token = $user->getToken();


        $content = $this->container->get('twig')->render(
            'mails/account_created.html.twig',
            [
                'user' => $user,
                'token' => $token,
                'baseUrl' => $url
            ]
        );

        return $this->sendMail($mail,$obj, $content);

    }

    public function sendMail($to, $obj, $content): int
    {
        $message = (new \Swift_Message($obj))
            ->setFrom('info@snow-trick.fr')
            ->setTo($to)
            ->setBody($content, 'text/html');

        return $this->mailer->send($message);
    }
}