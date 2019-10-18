<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\TrickRepository;
use App\Form\CommentType;
use App\Entity\Trick;
use App\Entity\Comment;

class TrickController extends AbstractController
{
    /**
     * Home page of the website
     *
     * @Route("/", name="tricks")
     */
    public function index(TrickRepository $repo)
    {
        $tricks = $repo->findBy([], ['createdAt' => 'DESC']);

        return $this->render('public/index.html.twig', [
            'controller_name' => 'TrickController',
            'tricks' => $tricks
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

            if($request->request->get('length')){
              $length = $request->request->get('length');
            }
            else {
              $length = 3;
            }

            return $this->render('public/tricks_list.html.twig', [
                'controller_name' => 'TrickController',
                'tricks' => $tricks,
                'start' => $tricksCount,
                'length' => $length
            ]);
        }
    }

    /**
     * Individual trick page
     *
     * @Route("/trick/{slug}", name="trick_show")
     */
    public function trickShow(Trick $trick, Request $request, SessionInterface $session)
    {
        $session->set('_security.main.target_path', $request->getUri());

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
            'controller_name' => 'TrickControllerShow',
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
                'controller_name' => 'TrickControllerShow',
                'trick' => $trick,
                'start' => $commentCount,
                'length' => 2
            ]);
        }
    }
}
