<?php

namespace App\Utils;

use App\Entity\User;
use Twig\Environment;

/**
 *
 */
class SendEmail
{
  private $renderer;
  private $mailer;

  function __construct(Environment $renderer, \Swift_Mailer $mailer)
  {
      $this->renderer = $renderer;
      $this->mailer = $mailer;
  }

  public function sendEmail(User $user)
  {
      $message = (new \Swift_Message('Confirmer votre inscription'))
        ->setFrom('noreply@snowtricks.com')
        ->setTo($user->getEmail())
        ->setBody(
            $this->renderer->render(
                'emails/registration.html.twig',
                ['user' => $user]
            ),
            'text/html'
        );
      $this->mailer->send($message);
  }
}
