<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationService
{
    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly EntityManagerInterface      $entityManager,
    ) {}

    public function registerUser(User $user, string $plainPassword): void
    {
        $hashedPassword = $this->userPasswordHasher->hashPassword(
            $user,
            $plainPassword
        );
        $user->setPassword($hashedPassword);

        $user->setRoles(['ROLE_USER']);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}