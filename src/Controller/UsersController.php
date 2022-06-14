<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use App\Form\UserFromType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController
{
    /**
     * @Route("/user/profile/{id}", methods={"GET"}, name="app_users")
     */
    public function showUser($id): Response
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

        return $this->render('users/index.html.twig', [
            'user' => $user
        ]);
    }


    /**
     * @Route("user/profile/edit/{id}", name="user_update", methods={"GET", "POST"})
     */
    public function updateProfile($id, Request $request ): Response
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $form = $this->createForm(UserFormType::class, $user);

        $form->handleRequest($request);
        $profileImg = $form->get('profileImg')->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            if ($profileImg) {
                if($user->getProfileImg() !== null) {
                    if (file_exists(
                        $this->getParameter('kernel.project_dir') . $user->getProfileImg()
                    )) {
                        $this->getParameter('kernel.project_dir') . $user->getProfileImg();
                    }

                    $newFileName = uniqid() . '.' . $profileImg->guessExtension();

                    try {
                        $profileImg->move(
                            $this->getParameter('kernel.project_dir') . '/public/uploads',
                            $newFileName
                        );

                    } catch (FileException $e) {
                        return new Response($e->getMessage());
                    }

                    $user->setProfileImg('/uploads/' . $newFileName);
                    $em->persist($user);
                    $em->flush();

                    return $this->redirectToRoute('app_users');
                }
            }

        } else {
            $user->setName($form->get('name')->getData());
            $user->setEmail($form->get('email')->getData());
            $user->setPassword($form->get('password')->getData());
            $user->setBirthday($form->get('birthday')->getData());
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('app_users');

        }


        return $this->render('users/update.html.twig', [
          'user' => $user,
           'form' => $form->createView()
      ]);
    }


}
