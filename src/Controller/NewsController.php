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
     * @Route("/news", name="neon_news")
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
     * @Route("/news/detail/{id}", name="detail_news", methods={"GET"})
     */
    public function showDetail($id)
    {
        $Denews = $this->getDoctrine()->getRepository(News::class)
            ->find($id);

        return $this->render('news/detail.html.twig', [
            'new' => $Denews
        ]);
    }

    // create news
    /**
     * @Route("/news/create", name="news_create", methods={"GET","POST"})
     */
    public function create(Request $request)
    {
        //create a news
        $Cnews = new News();
        $form = $this->createForm(NewsType::class, $Cnews);
        $form->handleRequest($request);

        // if press submit putting data to database
        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->render('news/create.html.twig', [
                'form' => $form->createView()
            ]);
        }
        //get data form
        $News = $form->getData();
        $imgpath = $form->get('img_path')->getData();

        if (!$imgpath) {
            // save old img if not change and back to home
            $em = $this->getDoctrine()->getManager();
            $em->persist($Cnews);
            $em->flush();

            $this->addFlash('notice', 'Submitted Successfully ');

            //back to news page then create a news successfully
            return $this->redirectToRoute('neon_news');
        }


        //when user create many same img name, uniquid method to genarate random id
        //guessextention will format backlile img
        $newFile = uniqid() . '.' . $imgpath->guessExtension();




        try {
            $imgpath->move(

                $this->getParameter('kernel.project_dir') . '/public/news',
                $newFile
            );
        } catch (FileException $e) {
            return new Response($e->getMessage());

        }

        $News->setImgPath('/news/' . $newFile);

        // get data from database
        $em = $this->getDoctrine()->getManager();

        // update database then create news
        $em->persist($Cnews);
        $em->flush();

        $this->addFlash('notice', 'Submitted Successfully ');

        //back to news page then create a news successfully
        return $this->redirectToRoute('neon_news');
    }



//Update function

    /**
     * @Route("/news/update/{id}", name="news_update", methods={"GET","POST"})
     */
    public
    function update($id, Request $request): Response
    {
        $Dnews = $this->getDoctrine()->getRepository(News::class)->find($id);
        $form = $this->createForm(NewsType::class, $Dnews);
        $form->handleRequest($request);
        // if press submit putting data to database
        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->render('news/update.html.twig', [
                'form' => $form->createView()
            ]);
        }

        $imgpath = $form->get('img_path')->getData();
        if (!$imgpath) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($Dnews);
            $em->flush();

            $this->addFlash('notice', 'update Successfully ');

            //back to news page then update a new successfully
            return $this->redirectToRoute('neon_news');
        }

        if (file_exists($this->getParameter('kernel.project_dir')
            . $Dnews->getImgPath())) {
            $this->getParameter('kernel.project_dir') . $Dnews->getImgPath();
        }

        $newFileName = uniqid() . '.' . $imgpath->guessExtension();

        try {
            $imgpath->move($this->getParameter('kernel.project_dir') . '/public/news',
                $newFileName);
        } catch (FileException $e) {
            return new Response($e->getMessage());
        }


        $Dnews->setImgPath('/news/' . $newFileName);


        $em = $this->getDoctrine()->getManager();

        $em->persist($Dnews);
        $em->flush();

        $this->addFlash('notice', 'update Successfully ');
        return $this->redirectToRoute('neon_news');
    }



    //delete function

    /**
     * @Route("/home/news/delete/{id}", name="news_delete", methods={"GET"})
     */
    public
    function delete($id)
    {
        $Denews = $this->getDoctrine()->getRepository(News::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($Denews);
        $em->flush();

        $this->addFlash('notice', 'Delete Successfully ');
        return $this->redirectToRoute('neon_news');
    }
}
