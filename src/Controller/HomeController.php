<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="neon_home")
     */
    public function index(): Response
    {

        // fetching all data from post table to display in home
        $posts = $this->getDoctrine()->getRepository(Post::class)
            ->findAll();

        $users = $this->getDoctrine()->getRepository(User::class)
            ->findAll();


        return $this->render('home/index.html.twig', [
            'posts' => $posts,
            'users' => $users
        ]);
    }



//    /**
//     * @Route("/users", name="neon_users")
//     */
//    public function showUser(): Response
//    {
//        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
//        return $this->render('home/user.html.twig', [
//            'users' => $users
//        ]);
//    }


    /**
     * @Route("/home/user/{id}", methods={"GET"}, name="neon_user")
     */
    public function showProfile($id): Response
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

        return $this->render('home/user.html.twig', [
            'user' => $user
        ]);

    }

}
