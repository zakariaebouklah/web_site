<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ZAKI")
 */
class ZakiController extends AbstractController
{
    #[Route('/zaki', name: 'app_zaki')]
    public function zaki(UserRepository $repository): Response
    {
        $users = $repository->findAll();
        $activeUsers = $repository->findActiveUsers();

        return $this->render('zaki/zaki.html.twig', [
            'users'=>$users,
            'activeUsers'=>$activeUsers,
        ]);
    }
}
