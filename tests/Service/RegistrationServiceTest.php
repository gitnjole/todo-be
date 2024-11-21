<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Service\RegistrationService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationServiceTest extends TestCase
{
    private UserPasswordHasherInterface|MockObject $passwordHasherMock;
    private EntityManagerInterface|MockObject $entityManagerMock;
    private RegistrationService $registrationService;

    protected function setUp(): void
    {
        $this->passwordHasherMock = $this->createMock(UserPasswordHasherInterface::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $this->registrationService = new RegistrationService(
            $this->passwordHasherMock,
            $this->entityManagerMock
        );
    }

    /**
     * Test user registration process
     */
    public function testRegisterUser(): void
    {
        $user = new User();
        $plainPassword = 'testPassword123';
        $hashedPassword = 'hashedPasswordValue';

        $this->passwordHasherMock
            ->expects($this->once())
            ->method('hashPassword')
            ->with($user, $plainPassword)
            ->willReturn($hashedPassword);

        $this->entityManagerMock
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (User $persistedUser) use ($hashedPassword) {
                $this->assertEquals($hashedPassword, $persistedUser->getPassword());

                $this->assertEquals(['ROLE_USER'], $persistedUser->getRoles());

                return true;
            }));

        $this->entityManagerMock
            ->expects($this->once())
            ->method('flush');

        $this->registrationService->registerUser($user, $plainPassword);
    }

    /**
     * Test that password is correctly hashed
     */
    public function testPasswordHashing(): void
    {
        $user = new User();
        $plainPassword = '123';
        $hashedPassword = 'hashedPasswordValue';

        $this->passwordHasherMock
            ->expects($this->once())
            ->method('hashPassword')
            ->with($user, $plainPassword)
            ->willReturn($hashedPassword);

        $reflectionMethod = new \ReflectionMethod($this->registrationService, 'registerUser');
        $reflectionMethod->invoke($this->registrationService, $user, $plainPassword);

        $this->assertEquals($hashedPassword, $user->getPassword());
    }

    /**
     * Test that user is assigned default ROLE_USER
     */
    public function testUserRoleAssignment(): void
    {
        $user = new User();
        $plainPassword = '123';

        $this->passwordHasherMock
            ->method('hashPassword')
            ->willReturn('hashedPassword');

        $this->registrationService->registerUser($user, $plainPassword);

        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }
}
