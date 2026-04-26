<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Enum\TaskStatus;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/task')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class TaskController extends AbstractController
{

    #[Route('/new', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        #[CurrentUser] User $user
    ): Response {

        $task = new Task();
        $form = $this->createForm(TaskType::class, $task, [
            'user' => $user,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $task->setUser($user);
            $task->setIsPinned(false);
            $task->setStatus(TaskStatus::PENDING);

            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/status', name: 'app_task_status', methods: ['POST'])]
    public function status(
        Task $task,
        EntityManagerInterface $entityManager,
        #[CurrentUser] User $user
    ): Response {
        if ($task->getUser() !== $user) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        if ($task->getStatus() === TaskStatus::PENDING) {
            $task->setStatus(TaskStatus::COMPLETED);
        } elseif ($task->getStatus() === TaskStatus::COMPLETED) {
            $task->setStatus(TaskStatus::ARCHIVED);
        } elseif ($task->getStatus() === TaskStatus::ARCHIVED) {
            $task->setStatus(TaskStatus::PENDING);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/{id}/pin', name: 'app_task_pin', methods: ['POST'])]
    public function pin(
        Task $task,
        EntityManagerInterface $entityManager,
        #[CurrentUser] User $user
    ): Response {
        if ($task->getUser() !== $user) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }
        $task->setIsPinned(!$task->isPinned());
        $entityManager->flush();

        return $this->redirectToRoute('app_dashboard');
    }
}
