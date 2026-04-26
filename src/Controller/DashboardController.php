<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Enum\TaskStatus;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class DashboardController extends AbstractController
{
    #[Route(['/dashboard', '/'], name: 'app_dashboard', methods: ['GET'])]
    public function index(
        #[CurrentUser] User $user,
        Request $request,
        TaskRepository $taskRepository,
        EntityManagerInterface $entityManager
    ): Response {

        $justToggledId = $request->getSession()->get('just_toggled_task_id');
        $request->getSession()->remove('just_toggled_task_id');

        $completedTasks = $taskRepository->findBy([
            'User' => $user,
            'status' => TaskStatus::COMPLETED,
        ]);

        $hasChanges = false;
        foreach ($completedTasks as $task) {
            if ($task->getId() !== $justToggledId) {
                $task->setStatus(TaskStatus::ARCHIVED);
                $hasChanges = true;
            }
        }

        if ($hasChanges) {
            $entityManager->flush();
        }

        $tasks = $taskRepository->findTasksSorted($user);

        return $this->render('dashboard/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }
}