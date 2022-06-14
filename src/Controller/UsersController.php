<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFromType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController
{
    /**
     * @Route("/profile/user", name="app_users")
     */
    public function index(): Response
    {
        return $this->render('users/index.html.twig', [
            'controller_name' => 'UsersController',
        ]);
    }

    /**
     * @Route("/profile/user/{id}", name="update_user")
     */
//    public function updateProfile($id, Request $request): Response
//    {
//
//        $user = $this->getDoctrine()->getRepository(User::class)->find($id);$form = $this->createForm(UserFromType::class)
//
//        return $this->render('users/update.html.twig', [
//          'user' => $user,
//           'form' => $form->createView()
//      ]);
//    }


}
