<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserServiceTest extends TestCase
{
    private UserPasswordHasherInterface|MockObject $passwordHasherMock;
    private UserRepository|MockObject $userRepositoryMock;
    private UserService $userService;

    protected function setUp(): void
    {
        $this->passwordHasherMock = $this->createMock(UserPasswordHasherInterface::class);
        $this->userRepositoryMock = $this->createMock(UserRepository::class);

        $this->userService = new UserService(
            $this->passwordHasherMock,
            $this->userRepositoryMock
        );
    }

    /**
     * Test user registration process.
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

        $this->userRepositoryMock
            ->expects($this->once())
            ->method('create')
            ->with($this->callback(function (User $persistedUser) use ($hashedPassword) {
                $this->assertEquals($hashedPassword, $persistedUser->getPassword());
                $this->assertEquals(['ROLE_USER'], $persistedUser->getRoles());

                return true;
            }));

        $this->userService->create($user, $plainPassword);
    }

    /**
     * Test that password is correctly hashed.
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

        $reflectionMethod = new \ReflectionMethod($this->userService, 'create');
        $reflectionMethod->invoke($this->userService, $user, $plainPassword);

        $this->assertEquals($hashedPassword, $user->getPassword());
    }

    /**
     * Test that user is assigned default ROLE_USER.
     */
    public function testUserRoleAssignment(): void
    {
        $user = new User();
        $plainPassword = '123';

        $this->passwordHasherMock
            ->method('hashPassword')
            ->willReturn('hashedPassword');

        $this->userService->create($user, $plainPassword);

        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }
}
