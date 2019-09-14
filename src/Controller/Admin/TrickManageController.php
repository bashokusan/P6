<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Utils\Slugger;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use App\Entity\Trick;
use App\Entity\Media;
use App\Form\CategoryType;
use App\Form\MediaUploadType;
use App\Entity\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Controller used to manage tricks
 *
 * @Route("/admin")
 * @IsGranted("ROLE_USER")
 */
class TrickManageController extends AbstractController
{
    /**
     * @Route("/", name="trick_admin")
     */
    public function index(TrickRepository $repo)
    {
        $tricks = $repo->findBy(['author' => $this->getUser()]);

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'TrickManageController',
            'tricks' => $tricks
        ]);
    }

    /**
     * @Route("/show/{id}", name="trick_admin_show")
     */
    public function show(Trick $trick)
    {
        return $this->render('admin/show.html.twig', [
            'controller_name' => 'TrickManageController',
            'trick' => $trick
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
            $trick->setSlug(Slugger::slugify($trick->getName()));
            $em = $this->getDoctrine()->getManager();
            $em->persist($trick);
            $em->flush();

            $imgFile = $form['media']->getData();
            if($imgFile){
                foreach ($imgFile as $img) {
                    $media = new Media();
                    $originalFilename = pathinfo($img->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = $originalFilename.'-'.uniqid().'.'.$img->guessExtension();

                    $img->move(
                        $this->getParameter('media_directory'),
                        $newFilename
                    );
                    $media->setSrc($newFilename);
                    $media->setTrick($trick);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($media);
                    $em->flush();
                }
            }

            $this->addFlash('success', 'Votre trick a bien été ajouté');
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
            $trick->setSlug(Slugger::slugify($trick->getName()));
            $trick->setUpdatedAt(new \DateTime());
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Votre trick a bien été mis à jour');
            return $this->redirectToRoute('trick_admin_show', ['id' => $trick->getId()]);
        }

        return $this->render('admin/edit.html.twig', [
            'controller_name' => 'TrickManageController',
            'form'  => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="trick_admin_delete", methods="DELETE")
     */
    public function delete(Trick $trick, Request $request)
    {
        if($this->isCsrfTokenValid('delete'.$trick->getId(), $request->get('_token'))){
            $em = $this->getDoctrine()->getManager();
            $em->remove($trick);
            $em->flush();
        }
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

            $this->addFlash('success', 'La catégorie a bien été ajoutée');
            return $this->redirectToRoute('trick_admin_new');
        }

        return $this->render('admin/category.html.twig', [
            'controller_name' => 'TrickManageController',
            'form'  => $form->createView()
        ]);
    }

    /**
     * @Route("/media/new", name="add_media")
     */
    public function addMedia(Request $request)
    {
        $media = new Media();
        $form = $this->createForm(MediaUploadType::class, $media);

        return $this->render('admin/media.html.twig', [
            'controller_name' => 'TrickManageController',
            'form'  => $form->createView()
        ]);
    }
}
