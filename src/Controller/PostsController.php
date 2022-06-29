<?php

namespace App\Controller;

use App\Entity\Post;
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

    // constructor will execute when objects created
    public function __construct(EntityManagerInterface $em, PostRepository $postRepository)
    {
        $this->em = $em;
        $this->postRepository = $postRepository;
    }




//    Show all posts

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





    //show detail of post

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


    // create post function

    /**
     * @Route("/home/posts/create", name="create_post", methods={"GET", "POST"})
     */
    public function createPost(Request $request): Response
    {
        //create new object
        $post = new Post();
        $form = $this->createForm(PostFormType::class, $post);
        $form->handleRequest($request);


        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->render('posts/create.html.twig', [
                'form' => $form->createView()
            ]);
        }

        //validation submit for post
        $newPost = $form->getData();
        $imgPath = $form->get('imgPath')->getData();

        if (!$imgPath) {
            //save the path of img into your project (/public/uploads/)
            $this->em->persist($newPost);
            $this->em->flush();

            return $this->redirectToRoute('neon_home');
        }


        // create newFileName to handle user upload many files have the same name
        // we use uniqid method to generate random id
        // after that we add concatenate a dot to the file extension back to it
        $newFileName = uniqid() . '.' . $imgPath->guessExtension();


        // trying to move the image to a location inside local project
        // the move method accepts two parameters
        // the first location is where you want to store it
        // $this->getParameter that will wrap a local parameter that we have called kernel.project_dir
        // the second parameter will be newFileName
        try {
            $imgPath->move(
            //kernel will set up bundles used by the application and provide them with app's configurations
                $this->getParameter('kernel.project_dir') . '/public/uploads/posts',
                $newFileName
            );
        } catch (FileException $e) {
            return new Response($e->getMessage());

        }

        $newPost->setImgPath('/uploads/posts/' . $newFileName);

        // set the current time for upload a post
        $date = new \DateTimeImmutable();
        $date->format("Y-m-d H:i:s");
        $currentTime = $date->setTimestamp(strtotime(date("Y-m-d H:i:s")));

        $newPost->setCreateAt($currentTime);

        // when user create a post it will take the id
        // of current user are posting.
        $newPost->setUser($this->getUser());


        // persist entity manager interface and flush the data
        // save the path of img into your project (/public/uploads/)
        $this->em->persist($newPost);
        $this->em->flush();

        return $this->redirectToRoute('neon_home');

    }




    // update post

    /**
     * @Route("/home/posts/update/{id}", name="update_post", methods={"GET", "POST"})
     */
    public function updatePost($id, Request $request): Response
    {
        $post = $this->postRepository->find($id);
        $form = $this->createForm(PostFormType::class, $post);
        $form->handleRequest($request);


        // it checks form is summited and valid current file is exits or not
        if (!$form->isSubmitted() || !$form->isValid() || $post->getImgPath() == null) {
            return $this->render('posts/update.html.twig', [
                'post' => $post,
                'form' => $form->createView()
            ]);
        }


        // value of form we get
        $imgPath = $form->get('imgPath')->getData();
        // checking whether image have been set or not
        // checking whether image path exits or not
        if (!$imgPath) {
            //set the rest of properties and change the data get method
            $post->setDescription($form->get('description')->getData());

            $this->em->flush();
            return $this->redirectToRoute('neon_home');
        }


        /*
            The role we are editing are has an image has been set
            To prevent the requirement when user add new image path
            it looks like to profitable in this
            situation so whe create an if statement

            if the file exist it will set the same image path that
            you import before
        */

        if (file_exists($this->getParameter('kernel.project_dir')
            . $post->getImgPath())) {
            $this->getParameter('kernel.project_dir') . $post->getImgPath();
        }


        // create newFileName to handle user upload many files have the same name
        // we use uniqid method to generate random id
        // after that we add concatenate a dot to the file extension back to it
        $newFileName = uniqid() . '.' . $imgPath->guessExtension();

        // trying to move the image to a location inside local project
        // the move method accepts two parameters
        // the first location is where you want to store it
        // $this->getParameter that will wrap a local parameter that we have called kernel.project_dir
        // the second parameter will be newFileName
        try {
            $imgPath->move($this->getParameter('kernel.project_dir') . '/public/uploads/posts',
                $newFileName);
        } catch (FileException $e) {
            // whenever it goes wrong to move an image and return a response and show the error
            return new Response($e->getMessage());
        }


        // update image path from input field to update img path
        // it accepts string as parameter
        //  final set imgPath for picture with new file name
        $post->setImgPath('/uploads/posts/' . $newFileName);
        $this->em->flush();

        return $this->redirectToRoute('neon_home');

    }



    // delete post

    /**
     * @Route("/home/posts/delete/{id}", methods={"GET", "DELETE"}, name="delete_post")
     */
    public function deletePost($id): Response
    {
        $post = $this->postRepository->find($id);
        $this->em->remove($post);
        $this->em->flush();

        return $this->redirectToRoute('neon_posts');
    }

}


