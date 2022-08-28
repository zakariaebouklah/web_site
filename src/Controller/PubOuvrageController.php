<?php

namespace App\Controller;

use App\Entity\Ouvrage;
use App\Form\DeleteFormType;
use App\Form\OuvrageFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN")
 */
#[Route('/pub')]
class PubOuvrageController extends AbstractController
{
    #[Route('/new_ouvrage', name: 'app_new_ouvrage')]
    #[Route('/edit_ouvrage/{slug}', name: 'app_edit_ouvrage')]
    public function formOuvrage(SluggerInterface $slugger, Request $request, string $_route, EntityManagerInterface $manager, Ouvrage $ouvrage = null): Response
    {
        if ($ouvrage === null)
        {
            if ($_route === "app_edit_ouvrage")
            {
                throw new NotFoundHttpException();
            }
            $ouvrage = new Ouvrage();
        }

        $form = $this->createForm(OuvrageFormType::class, $ouvrage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {

            $document = $form->get('document')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($document) {
                $originalFilename = pathinfo($document->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$document->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $document->move(
                        $this->getParameter('documents_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $ouvrage->setDocument($newFilename);
            }

            $ouvrage->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($ouvrage);
            $manager->flush();

            return $this->redirectToRoute("app_ouvrage", ['slug'=>$ouvrage->getSlug()]);
        }

        return $this->render("pub/ouvrage.html.twig", [
            'form'=>$form->createView(),
            'edit'=>$ouvrage->getTitle() !== null
        ]);
    }

    #[Route('/delete/{slug}', name: "app_delete_ouvrage")]
    public function deleteOuvrage(Ouvrage $ouvrage, EntityManagerInterface $manager, Request $request): Response
    {
        $form = $this->createForm(DeleteFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $manager->remove($ouvrage);
            $manager->flush();

            return $this->redirectToRoute("app_ouvrages");
        }

        return $this->render("pub/delete.html.twig", [
            'form'=>$form
        ]);
    }
}
