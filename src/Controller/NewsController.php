<?php

namespace App\Controller;

use App\Entity\News;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewsController extends AbstractController
{

    /**
     * @Route("home/news", name="neon_news")
     */
    public function listNews(): Response
    {

        $news = $this->getDoctrine()->getManager()->getRepository(News::class)
        ->findAll();

        return $this->render('news/index.html.twig', [
            'news' => $news,
        ]);
    }


   // show detail of news

    /**
     * @Route("/home/news/detail/{id}", name="detail_news", methods={"GET"})
     */
    public function showDetail($id): Response
    {
        $news = $this->newsRepository->find($id);

        return $this->render('news/detail.html.twig', [
            'news' => $news
        ]);
    }

}
