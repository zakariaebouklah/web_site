<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileFormType;
use App\Form\UserSettingsFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserEditController extends AbstractController
{
    #[Route('/user/stuffs/{username}', name: 'app_user_stuffs')]
    public function index(User $user): Response
    {
        if ($this->getUser() === null)
        {
            return $this->redirectToRoute("app_login");
        }

        if ($user !== $this->getUser())
        {
            return $this->redirectToRoute("app_accueil");
        }

        return $this->render('user_edit/index.html.twig');
    }

    #[Route('/user/profile/{username}', name: 'app_user_profile')]
    public function profile(Request $request, EntityManagerInterface $manager, User $user, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProfileFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $profile = $form->get('profile')->getData();
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($profile) {
                $originalFilename = pathinfo($profile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$profile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $profile->move(
                        $this->getParameter('profile_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $user->setProfile($newFilename);
            }

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute("app_accueil");
        }

        return $this->render('user_edit/userProfile.html.twig', [
            'form'=>$form->createView()
        ]);
    }

    #[Route('/user/settings/{username}', name: 'app_user_settings')]
    public function settings(Request $request, EntityManagerInterface $manager, User $user, UserPasswordHasherInterface $hasher): Response
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

        return $this->render('user_edit/userSettings.html.twig', [
            'form'=>$form->createView()
        ]);
    }
}
