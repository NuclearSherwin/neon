<?php

namespace App\Controller;

use App\Entity\News;
use App\Entity\Post;
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
     * @Route("/home/news/create", name="create_news", methods={"GET", "POST"})
     */
    public function createNews(Request $request): Response
    {
        //create Object
        $news = new news();
        $form = $this->createForm(NewsFormType::class, $news);

        $form->handleRequest($request);

        //validation submit for News
        if ($form->isSubmitted() && $form->isValid()) {
            $newNews = $form->getData();
            $imgpath = $form->get('imgPath')->getData();
            if ($imgpath) {
                // identifier the dot at the end of the picture
                $newFileName = uniqid() . '.' . $imgpath->guessExtension();

                try {
                    $imgpath->move(
                    //kernel will set up bundles used by the application and provide them with app's configurations
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFileName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());

                }

                $newNews->setImgPath('/uploads/' . $newFileName);
                $date = new \DateTimeImmutable();
                $date->format("Y-m-d H:i:s");
                $currentTime = $date->setTimestamp( strtotime(date("Y-m-d H:i:s")));
                $newNews->setCreateAt($currentTime);
            }

            //save the path of img into your project (/public/uploads/)
            $this->em->persist($newNews);
            $this->em->flush();

            return $this->redirectToRoute('neon_News');
        }


        return $this->render('News/create.html.twig', [
            'form' => $form->createView()
        ]);

    }

}
