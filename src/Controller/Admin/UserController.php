<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Form\UploadFileType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserController extends AbstractController
{
    /**
     * User Profile Page
     * 
     * @Route("/profile/{pseudo}", name="profile")
     */
    public function index(User $user, Request $request)
    {
        $form = $this->createForm(UploadFileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $avatarFile = $form['avatar']->getData();
            if($avatarFile){
                $originalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$avatarFile->guessExtension();

                $avatarFile->move(
                    $this->getParameter('avatar_directory'),
                    $newFilename
                );

                $user->setAvatar($newFilename);
                $this->getDoctrine()->getManager()->flush();
            }
        }

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'user' => $user,
            'form' => $form->createView()
        ]);
    }
}
