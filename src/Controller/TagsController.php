<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\PostFormType;
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
     * @Route("home/tags", name="neon_tags")
     */
    public function listNews(): Response
    {

        $tags = $this->tagResponsitory->findAll();

        return $this->render('tags/index.html.twig', [
            'tags' => $tags,
        ]);
    }
}
