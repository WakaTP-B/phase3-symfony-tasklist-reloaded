<?php
namespace App\Controller;

use App\Entity\User;
use App\Enum\TaskStatus;
use App\Repository\FolderRepository;
use App\Repository\PriorityRepository;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class DashboardController extends AbstractController
{
    #[Route(['/dashboard', '/'], name: 'app_dashboard', methods: ['GET'])]
    public function index(
        Request $request,
        #[CurrentUser] User $user,
        TaskRepository $taskRepository,
        FolderRepository $folderRepository,
        PriorityRepository $priorityRepository
    ): Response {
        $statusValue = $request->query->get('status');
        $folderId    = $request->query->getInt('folder', 0);
        $priorityId  = $request->query->getInt('priority', 0);

        $status = $statusValue ? TaskStatus::tryFrom($statusValue) : null;

        $tasks = $taskRepository->findTasksSorted(
            $user,
            $status,
            $folderId ?: null,
            $priorityId ?: null
        );

        $folders    = $folderRepository->findBy(['User' => $user]);
        $priorities = $priorityRepository->findAvailableForUser($user);

        return $this->render('dashboard/index.html.twig', [
            'tasks'          => $tasks,
            'folders'        => $folders,
            'priorities'     => $priorities,
            'activeStatus'   => $statusValue,
            'activeFolderId' => $folderId,
            'activePriority' => $priorityId,
        ]);
    }
}