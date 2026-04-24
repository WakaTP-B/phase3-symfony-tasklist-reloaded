<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Repository\TaskRepository;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class DashboardController extends AbstractController
{
    #[Route(['/dashboard', '/'], name: 'app_dashboard', methods: ['GET'])]
    public function index(
        #[CurrentUser] User $user,
        TaskRepository $taskRepository
    ): Response {
        $tasks = $taskRepository->findBy(
            ['User' => $user],
            ['isPinned' => 'DESC', 'id' => 'DESC']
        );

        return $this->render('dashboard/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }
}
