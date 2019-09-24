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
     * @Route("/", name="tricks")
     */
    public function index()
    {
        return $this->render('public/index.html.twig', [
            'controller_name' => 'TrickController',
        ]);
    }

    /**
     * @Route("/more", name="loadMoreTricks")
     */
    public function loadMoreTricks(Request $request, TrickRepository $repo)
    {
        $tricks = $repo->findBy([], ['createdAt' => 'DESC']);

        if($request->isXmlHttpRequest()){
            $tricksCount = $request->request->get('tricksCount');
            return $this->render('public/tricks_list.html.twig', [
                'controller_name' => 'TrickController',
                'tricks' => $tricks,
                'start' => $tricksCount,
                'length' => 3
            ]);
        }
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

        return $this->render('public/trick_show.html.twig', [
            'controller_name' => 'TrickController',
            'trick' => $trick,
            'form'  => $form->createView()
        ]);
    }

    /**
     * @Route("/trick/more/{slug}", name="loadMoreComments")
     */
    public function loadMoreComments(Trick $trick, Request $request)
    {
        if($request->isXmlHttpRequest()){
            $commentCount = $request->request->get('commentCount');
            return $this->render('public/comments.html.twig', [
                'controller_name' => 'BlogController',
                'trick' => $trick,
                'start' => $commentCount,
                'length' => 2
            ]);
        }
    }
}
