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
//    // create post function

//    /**
//     * @Route("/home/tags/create", name="create_tag", methods={"GET", "POST"})
//     */
//    public function createPost(Request $request): Response
//    {
//        //create new object
//        $tags = new Tags();
//        $form = $this->createForm(TagFormType::class, $tags);
//
//        $form->handleRequest($request);
//
//        //validation submit for post
//        if ($form->isSubmitted() && $form->isValid()) {
//            $newTag = $form->getData();
//            ;
//
//            }
//
//            //save the path of img into your project (/public/uploads/)
//            $this->em->persist($newTag);
//            $this->em->flush();
//
//            return $this->redirectToRoute('neon_tags');
//        }
//
//
//        return $this->render('tags/create.html.twig', [
//            'form' => $form->createView()
//        ]);
//
//    }
}

