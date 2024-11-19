<?php

namespace App\Security;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, ['TASK_VIEW', 'TASK_EDIT', 'TASK_DELETE'])
            && $subject instanceof Task;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Task $task */
        $task = $subject;

        return match($attribute) {
            'TASK_VIEW' => $this->canView($task, $user),
            'TASK_EDIT' => $this->canEdit($task, $user),
            'TASK_DELETE' => $this->canDelete($task, $user),
            default => false,
        };
    }

    private function canView(Task $task, User $user): bool
    {
        return $task->getUserTask() === $user;
    }

    private function canEdit(Task $task, User $user): bool
    {
        return $task->getUserTask() === $user;
    }

    private function canDelete(Task $task, User $user): bool
    {
        return $task->getUserTask() === $user;
    }
}