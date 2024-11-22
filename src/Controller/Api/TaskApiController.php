<?php

namespace App\Controller\Api;

use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TaskApiController extends AbstractController
{
    #[Route('/api/all', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function all(TaskService $taskService): JsonResponse
    {
        $user = $this->getUser();
        $tasks = $taskService->fetchAll($user);

        return $this->json($tasks, context: ['groups' => ['task:read']]);
    }
}
