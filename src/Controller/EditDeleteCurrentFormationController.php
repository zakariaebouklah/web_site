<?php

namespace App\Controller;

use App\Entity\AnnonceFormation;
use App\Form\AnnonceFormType;
use App\Form\DeleteFormType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class EditDeleteCurrentFormationController extends AbstractController
{
    #[Route('/delete/current_formation/{slug}', name: 'app_delete_current_formation')]
    public function delete(Request $request, EntityManagerInterface $manager, AnnonceFormation $annonceFormation): Response
    {
        $form = $this->createForm(DeleteFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $manager->remove($annonceFormation);
            $manager->flush();

            return $this->redirectToRoute("app_formation_doctorale");
        }

        return $this->render('web_site/delete.html.twig', [
            'form'=>$form->createView()
        ]);
    }

    #[Route('/edit/current_formation/{slug}', name: 'app_edit_current_formation')]
    public function edit(AnnonceFormation $annonceFormation, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(AnnonceFormType::class, $annonceFormation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($annonceFormation);
            $manager->flush();

            return $this->redirectToRoute("app_formation_courante");
        }

        return $this->render('formation/newFormationEdition.html.twig', [
            'form'=>$form->createView(),
            'edit'=>true
        ]);
    }
}
