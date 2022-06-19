<?php

namespace App\Controller;

use App\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TagsController extends AbstractController
{
//    private $em;
//    private $postResponsitory;
//    public function __construct(EntityManagerInterface $em, TagRepository $tagRepository)
//    {
//        $this->em = $em;
//        $this->TagRepository = $tagRepository;
//    }


//    /**
//     * @Route("/tags", name="app_tags")
//     */
//    public function index(): Response
//    {
//        return $this->render('tags/index.html.twig', [
//            'controller_name' => 'TagsController',
//        ]);
//    }


    /**
     * @Route("home/tags", name="app_tags")
     */
    public function listNews(): Response
    {

        $tags = $this->getDoctrine()->getManager()->getRepository(Tag::class)
            ->findAll();

        return $this->render('tags/index.html.twig', [
            'tags' => $tags,
        ]);
    }
}
