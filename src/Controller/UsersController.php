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
    public function updateProfile($id, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);

        $form = $this->createForm(UserFromType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user->setName($request->request->get('user')['name']);
            $user->setEmail($request->request->get('user')['email']);
            $user->setPassword($request->request->get('user')['password']);
            $user->setProfileImg($request->request->get('user')['profileImg']);
            $user->setBirthday(\DateTime::createFromFormat('Y-m-d', $request->request->get('todo')['due_date']));
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }

        return $this->render('users/update.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }


}
