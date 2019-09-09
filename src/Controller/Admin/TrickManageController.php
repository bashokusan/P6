<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\TrickType;
use App\Entity\Trick;
use App\Form\CategoryType;
use App\Entity\Category;

/**
 * Controller used to manage tricks
 *
 * @Route("/admin")
 */
class TrickManageController extends AbstractController
{
    /**
     * @Route("/", name="trick_admin")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'TrickManageController',
        ]);
    }

    /**
     * @Route("/show", name="trick_admin_show")
     * On mettra l'id du trick dans l'url
     */
    public function show()
    {
        return $this->render('admin/show.html.twig', [
            'controller_name' => 'TrickManageController',
        ]);
    }

    /**
     * @Route("/new", name="trick_admin_new")
     */
    public function new(Request $request)
    {
        $trick = new Trick();
        $trick->setAuthor($this->getUser());

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($trick);
            $em->flush();
            $this->addFlash('success', 'Le trick a bien été ajouté');

            return $this->redirectToRoute('trick_admin');
        }

        return $this->render('admin/new.html.twig', [
            'controller_name' => 'TrickManageController',
            'form'  => $form->createView()
        ]);
    }

    /**
     * @Route("/edit/{id}", name="trick_admin_edit")
     */
    public function edit(Trick $trick, Request $request)
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->render('admin/edit.html.twig', [
            'controller_name' => 'TrickManageController',
            'form'  => $form->createView()
        ]);
    }

    /**
     * @Route("/delete", name="trick_admin_delete")
     * On mettra l'id du trick dans l'url
     */
    public function delete()
    {
        return $this->redirectToRoute('trick_admin');
    }


    /**
     * @Route("/category/new", name="category_new")
     */
    public function newCategory(Request $request)
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
        }

        return $this->render('admin/category.html.twig', [
            'controller_name' => 'TrickManageController',
            'form'  => $form->createView()
        ]);
    }
}