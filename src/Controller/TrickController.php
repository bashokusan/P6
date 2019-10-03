<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\TrickRepository;
use App\Form\CommentType;
use App\Entity\Trick;
use App\Entity\Comment;

class TrickController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="tricks")
     */
    public function index(TrickRepository $repo)
    {
        $tricks = $repo->findAll();

        return $this->render('trick/index.html.twig', [
            'controller_name' => 'TrickController',
            'tricks' => $tricks
        ]);
    }

    /**
     * @Route("/trick/{slug}", name="trick_show")
     */
    public function trickShow(Trick $trick, Request $request)
    {
        $comment = new Comment();
        $comment->setAuthor($this->getUser());
        $comment->setTrick($trick);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        return $this->render('trick/trick_show.html.twig', [
            'controller_name' => 'TrickController',
            'trick' => $trick,
            'form'  => $form->createView()
        ]);
    }
}
