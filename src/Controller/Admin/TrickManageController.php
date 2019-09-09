<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\TrickType;
use App\Entity\Trick;

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
    public function new()
    {
        $trick = new Trick();

        $form = $this->createForm(TrickType::class, $trick);

        return $this->render('admin/new.html.twig', [
            'controller_name' => 'TrickManageController',
            'form'  => $form->createView()
        ]);
    }

    /**
     * @Route("/edit", name="trick_admin_edit")
     */
    public function edit()
    {
        $form = $this->createForm(TrickType::class, $trick);

        return $this->render('admin/edit.html.twig', [
            'controller_name' => 'TrickManageController',
            'form'  => $form->createView()
        ]);
    }
}
