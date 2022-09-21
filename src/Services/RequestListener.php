<?php

namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Security;

class RequestListener
{
    public function __construct(private EntityManagerInterface $manager, private Security $security) {}

    public function onKernelRequest(RequestEvent $requestEvent): void
    {
        $user = $this->security->getUser();

        if ($user)
        {
            /**
             * @var User $user
             */

            $user->setLastSeen(new \DateTimeImmutable());
            $this->manager->persist($user);
            $this->manager->flush();
        }
    }
}