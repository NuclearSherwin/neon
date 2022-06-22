<?php

namespace App\Controller;

use App\Entity\News;
use App\Form\NewsType;
use ContainerO4St7eP\getNewsRepositoryService;
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
    public function showDetail($id)
    {
        $Denews = $this->getDoctrine()->getRepository(News::class)
            ->find($id);

        return $this->render('news/detail.html.twig', [
            'new' => $Denews
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

            //back to news page then create a news successfully
            return $this->redirectToRoute('neon_news');
        }
        return $this->render('news/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    //Update function
    /**
     * @Route("/home/news/update/{$id}", name="news_update", methods={"GET","POST"})
     */
    public function update(Request $request, $id)
    {
        $Dnews = $this->getDoctrine()-getRepository(News::class)->find($id);
        $form = $this->createForm(NewsType::class, $Dnews);
        $form->handleRequest($request);
        // if press submit putting data to database
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($Dnews);
            $em->flush();

            $this-> addFlash('notice','update Successfully ');

            //back to news page then update a news successfully
            return $this->redirectToRoute('neon_news');
        }
        return $this->render('news/update.html.twig', [
            'form' => $form->createView()
        ]);
    }

    //delete function

    /**
     * @Route("/home/news/delete/{$id}", name="news_delete", methods={"GET"})
     */
    public function delete($id){
        $Denews = $this->getDoctrine()->getRepository(News::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($Denews);
        $em->flush();

        $this->addFlash('notice','Delete Successfully ');
        return $this->redirectToRoute('neon_news');
    }
}
