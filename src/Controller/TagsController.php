<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagFormType;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TagsController extends AbstractController
{
    private EntityManagerInterface $em;
    private $tagResponsitory;

    public function __construct(EntityManagerInterface $_em, TagRepository $tagRepository)
    {
        $this->em = $_em;
        $this->tagResponsitory = $tagRepository;
    }

    /**
     * @Route("/tags", name="neon_tags")
     */
    public function listTags(): Response
    {
        $tags = $this->tagResponsitory->findAll();
        return $this->render('tags/index.html.twig', [
            'tags' => $tags,
        ]);
    }


    /**
     * @Route("/tags/detail/{id}", name="detail_tag")
     */
    public function showDetail($id): Response
    {
        $tag = $this->tagResponsitory->find($id);

        return $this->render('tags/detail.html.twig', [
            'tag' => $tag
        ]);
    }


    /**
     * @Route("/tags/create", name="create_tag", methods={"GET","POST"})
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

    public function saveChanges($form, Request $request, Tag $tag)
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($tag);
            $em->flush();

            return true;
        }
        return false;
    }

    /**
     * @Route("/tags/update/{id}", name="tag_update")
     */
    public function editAction($id, Request $request)
    {
        $tag = $this->tagResponsitory->find($id);
        $form = $this->createForm(TagFormType::class, $tag);

        if ($this->saveChanges($form, $request, $tag)) {
            $this->addFlash(
                'notice',
                'Todo Edited'
            );
        }
        return $this->render('tags/update.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/tags/delete/{id}", name="tag_delete")
     */
    public function delete($id): Response
    {

        $em = $this->getDoctrine()->getManager();
        $tag = $this->tagResponsitory->find($id);
        $em->remove($tag);
        $em->flush();

        $this->addFlash(
            'error',
            'Tag deleted'
        );

        return $this->redirectToRoute('neon_tags');
    }
}

