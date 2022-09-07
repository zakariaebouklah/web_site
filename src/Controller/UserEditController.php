<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserSettingsFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserEditController extends AbstractController
{
    #[Route('/user/edit/{username}', name: 'app_user_edit')]
    public function index(Request $request, EntityManagerInterface $manager, User $user, UserPasswordHasherInterface $hasher): Response
    {
        if ($this->getUser() === null)
        {
            return $this->redirectToRoute("app_login");
        }

        if ($user !== $this->getUser())
        {
            return $this->redirectToRoute("app_accueil");
        }

        $form = $this->createForm(UserSettingsFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
//            dd($data->getUsername());
//            dd($form->get('confirm_new_password')->getData());
//            dd($hasher->hashPassword($user, $data->getPassword()));
//            dd($hasher->isPasswordValid($user, $form->get('old_password')->getData()));
//            dd($form->get('confirm_new_password')->getData() === $data->getPassword());
            if ($form->get('confirm_new_password')->getData() === $data->getPassword() && $hasher->isPasswordValid($user, $form->get('old_password')->getData()))
            {
                $user->setUsername($data->getUsername());
                $user->setPassword($hasher->hashPassword($user, $data->getPassword()));

                $manager->persist($user);
                $manager->flush();

                return $this->redirectToRoute("app_accueil");
            }
        }

        return $this->render('user_edit/index.html.twig', [
            'form'=>$form->createView()
        ]);
    }
}
