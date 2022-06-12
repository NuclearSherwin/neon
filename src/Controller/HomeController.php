<?php

namespace App\Controller;

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
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
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
     * @Route("/user/{id}", methods={"GET"}, name="neon_user")
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
