<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * Registration Page
     * Create a token for the user and save it into the database
     * Send an email with a url with the token in param
     *
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator, \Swift_Mailer $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                ))
                ->setToken(bin2hex(random_bytes(16)))
                ;

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $message = (new \Swift_Message('Confirmer votre inscription'))
              ->setFrom('noreply@snowtricks.com')
              ->setTo($user->getEmail())
              ->setBody(
                  $this->render(
                      'emails/registration.html.twig',
                      ['user' => $user]
                  ),
                  'text/html'
              );
            $mailer->send($message);

            $this->addFlash('success','Votre compte a bien été créé, un mail de confirmation vous a été envoyé.');
            return $this->redirectToRoute('trick_admin');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * Get the token from the url from the email and verify it with the one saved in the database
     *
     * @Route("/confirm", name="confirm")
     */
    public function confirm(Request $request, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()
        ->getRepository(User::class)
        ->find($request->query->get('id'));

        if($request->query->get('token') === $user->getToken()){
            $user->setConfirm(1);
            $entityManager->flush();

            $this->addFlash('success','Votre compte a bien été validé');
            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }else{
            $this->addFlash('warning','L\'url est invalide, veuillez réessayer.');
            return $this->redirectToRoute('app_login');
        }
    }
}
