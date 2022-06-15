<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostFormType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostsController extends AbstractController
{
    private $em;
    private $postRepository;

    //constructor will execute when objects created
    public function __construct(EntityManagerInterface $em, PostRepository $postRepository)
    {
        $this->em = $em;
        $this->postRepository = $postRepository;
    }


    /**
     * @Route("/home/posts", name="neon_posts")
     */
    public function index(): Response
    {

        $posts = $this->postRepository->findAll();

        return $this->render('posts/index.html.twig', [
            'posts' => $posts,
        ]);
    }



    /**
     * @Route("/home/posts/create", name="create_posts", methods={"GET", "POST"})
     */
    public function createPost(Request $request): Response
    {
        //create new object
        $post = new Post();
        $form = $this->createForm(PostFormType::class, $post);

        $form->handleRequest($request);

        //validation submit for post
        if ($form->isSubmitted() && $form->isValid()) {
            $newPost = $form->getData();
            $imgPath = $form->get('imgPath')->getData();
            if ($imgPath) {
                // identifier the dot at the end of the picture
                $newFileName = uniqid() . '.' . $imgPath->guessExtension();

                try {
                    $imgPath->move(
                    //kernel will set up bundles used by the application and provide them with app's configurations
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFileName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());

                }

                $newPost->setImgPath('/uploads/' . $newFileName);
            }

                //save the path of img into your project (/public/uploads/)
                $this->em->persist($newPost);
                $this->em->flush();

                return $this->redirectToRoute('neon_posts');
        }


        return $this->render('posts/create.html.twig', [
            'form' => $form->createView()
        ]);

    }
}


