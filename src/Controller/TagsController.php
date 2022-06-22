<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagFormType;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

class TagsController extends AbstractController
{
    private $em;
    private $tagResponsitory;
    public function __construct(EntityManagerInterface $em, TagRepository $tagRepository)
    {
        $this->em = $em;
        $this->tagResponsitory = $tagRepository;
    }



    /**
     * @Route("home/tags", name="neon_tags")
     */
    public function listNews(): Response
    {

        $tags = $this->tagResponsitory->findAll();

        return $this->render('tags/index.html.twig', [
            'tags' => $tags,
        ]);
    }


    /**
     * @Route("/home/tags/detail/{id}", name="detail_tag")
     */
    public function showDetail($id): Response
    {
        $tag = $this->tagResponsitory->find($id);

        return $this->render('tags/detail.html.twig', [
            'tag' => $tag
        ]);
    }



    /**
     * @Route("/todo/create", name="create_tag", methods={"GET","POST"})
     */
    public function createAction(Request $request)
    {
        $tag = new Tag();
        $form = $this->createForm(TagFormType::class, $tag);

        if ($this->saveChanges($form, $request, $tag)) {
            $this->addFlash(
                'notice',
                'Todo Added'
            );

            return $this->redirectToRoute('neon_tags');
        }

        return $this->render('tags/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function saveChanges($form, $request, $tag)
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            $tag->setName($request->request->get('tag')['name']);
//
//            $tag->setDescription($request->request->get('tag')['description']);


            $em = $this->getDoctrine()->getManager();
            $em->persist($tag);
            $em->flush();

            return true;
        }
        return false;
    }
}

