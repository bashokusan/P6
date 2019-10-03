<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Utils\Slugger;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use App\Entity\Trick;
use App\Entity\Image;
use App\Form\CategoryType;
use App\Entity\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

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
            
            $files = $trick->getImages();
            foreach ($files as $img) {
                $file = $img->getFile();
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$file->guessExtension();
                $img->setSrc($newFilename);
                $file->move($this->getParameter('media_directory'), $newFilename);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($trick);
            $em->flush();
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
        $this->denyAccessUnlessGranted('TRICK_EDIT', $trick);
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $trick->setSlug(Slugger::slugify($trick->getName()));
            $trick->setUpdatedAt(new \DateTime());
            $files = $trick->getImages();
            foreach ($files as $img) {
                if(!null == $file = $img->getFile()){
                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = $originalFilename.'-'.uniqid().'.'.$file->guessExtension();
                    $img->setSrc($newFilename);
                    $file->move($this->getParameter('media_directory'), $newFilename);
                }
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($trick);
            dump($trick);
            $em->flush();

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
    public function delete(Trick $trick, Request $request, Filesystem $filesystem)
    {
        $this->denyAccessUnlessGranted('TRICK_DELETE', $trick);
        if($this->isCsrfTokenValid('delete'.$trick->getId(), $request->get('_token'))){
            $files = $trick->getImages();
            foreach ($files as $img) {
                $filesystem->remove($this->getParameter('media_directory').'/'.$img->getSrc());
            }
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
}
