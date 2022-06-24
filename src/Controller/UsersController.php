<?php

namespace App\Controller;

use App\Form\UserFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController
{
    private $em;
    private $userRepository;


    public function __construct(UserRepository $userRepository, EntityManagerInterface $em)
    {
        $this->userRepository = $userRepository;
        $this->em = $em;
    }


    /**
     * @Route("/user/profile/{id}", methods={"GET"}, name="profile_user")
     */
    public function showUser($id): Response
    {
        $user = $this->userRepository->find($id);

        return $this->render('users/index.html.twig', [
            'user' => $user
        ]);
    }


    /**
     * @Route("/user/profile/update/{id}", name="update_user", methods={"GET", "POST"})
     */
    public function updateProfile($id, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $this->userRepository->find($id);
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);
        $profileImg = $form->get('profileImg')->getData();


        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->render('users/update.html.twig', [
                'user' => $user,
                'form' => $form->createView()
            ]);
        }

        if (!$profileImg || $user->getProfileImg() === null) {
            $name = $form->get('name')->getData();
//            $email = $form->get('email')->getData();
//            $plainPassword = $form->get('plainPassword')->getData();
            $birthDay = $form->get('birthday')->getData();

            //encode for password
//            $hashPassword = $userPasswordHasher->hashPassword(
//                $user,
//                $plainPassword
//            );

            $user->setName($name);
//            $user->setEmail($email);
//            $user->setPassword($hashPassword);
            $birthDay->format('Y-m-h H:i:s');

            $this->em->flush();
            return $this->redirectToRoute('neon_home');
        }

        $fileName = $this->getParameter('kernel.project_dir') . $user->getProfileImg();
        if (file_exists($fileName)) {
            $this->GetParameter('kernel.project_dir') . $user->getProfileImg();
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
        $this->em->flush();

        return $this->redirectToRoute('neon_home');

    }


    /**
     * @Route("/user/profile/delete/{id}", methods={"GET", "DELETE"}, name="delete_user")
     */
    public function deleteUser($id): Response
    {
        $user = $this->userRepository->find($id);
        $this->em->remove($user);
        $this->em->flush();

        return $this->redirectToRoute('profile_user');

    }


}
