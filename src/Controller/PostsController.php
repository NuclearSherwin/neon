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
     * @Route("/home/posts/detail/{id}", name="detail_post", methods={"GET"})
     */
    public function showDetail($id): Response
    {
        $post = $this->postRepository->find($id);

        return $this->render('posts/detail.html.twig', [
            'post' => $post
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


    /**
     * @Route("/home/posts/update/{id}", name="update_post", methods={"GET", "POST"})
     */
    public function updatePost($id, Request $request): Response
    {
        $post = $this->postRepository->find($id);
        $form = $this->createForm(PostFormType::class, $post);

        $form->handleRequest($request);

        //for picture edit
        $imgPath = $form->get('imgPath')->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            if ($imgPath) {
                // checking whether image input is not null
                if ($post->getImgPath() !== null) {
                    if (file_exists($this->getParameter('kernel.project_dir')
                        . $post->getImgPath())) {
                        $this->getParameter('kernel.project_dir') . $post->getImgPath();
                    }


                    //making the dot at the end of string when we input the picture into
                    $newFileName = uniqid() . '.' . $imgPath->guessExtension();


                    //trying to get the picture into public
                    try {
                        $imgPath->move($this->getParameter('kernel.project_dir') . '/public/uploads',
                            $newFileName);
                    } catch (FileException $e) {
                        return new Response($e->getMessage());
                    }

                    //final set imgPath for picture with new file name
                    $post->setImgPath('/uploads/' . $newFileName);
                    $this->em->flush();

                    return $this->redirectToRoute('neon_posts');
                }
            } else {
                //set the rest of properties
                $post->setDescription($form->get('description')->getData());

                $this->em->flush();
                return $this->redirectToRoute('neon_posts');
            }
        }


        return $this->render('posts/update.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/home/posts/delete/{id}", methods={"GET", "DELETE"}, name="delete_post")
     */
    public function deletePost($id) : Response
    {
        $post = $this->postRepository->find($id);
        $this->em->remove($post);
        $this->em->flush();

        return $this->redirectToRoute('neon_posts');
    }

}


