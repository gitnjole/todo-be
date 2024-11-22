<?php

namespace App\Controller\Api;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class SecurityApiController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(#[CurrentUser] UserInterface $user, JWTTokenManagerInterface $JWTTokenManager): JsonResponse
    {
        return new JsonResponse([
            'token' => $JWTTokenManager->create($user),
        ]);
    }

    #[Route('/api/authorize', name: 'api_authorize', methods: ['GET'])]
    public function authorize(): JsonResponse
    {
        return new JsonResponse([
            'authenticated' => true,
            'user' => $this->getUser()->getUserIdentifier(),
        ]);
    }

    public function logout(TokenStorageInterface $tokenStorage): JsonResponse
    {
        $tokenStorage->setToken(null);

        return new JsonResponse([
            'message' => 'Logged out',
        ]);
    }
}
