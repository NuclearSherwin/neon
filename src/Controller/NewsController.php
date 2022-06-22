<?php

namespace App\Controller;

use App\Entity\News;
use App\Entity\Post;
use App\Form\NewsType;
use App\Form\PostFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
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

    // create newws function

    /**
     * @Route("/home/news/create", name="news_create", methods={"GET","POST"})
     */
    public function create(Request $request)
    {
        $Cnews = new News();
        $form = $this->createForm(NewsType::class, $Cnews);
        $form->handleRequest($request);
        // if press submit putting data to database
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($Cnews);
            $em->flush();

            $this-> addFlash('notice','Submitted Successfully ');

            //backe to news page then create a news successfully
            return $this->redirectToRoute('neon_news');
        }
        return $this->render('news/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
