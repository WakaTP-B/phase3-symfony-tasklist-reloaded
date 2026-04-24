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

    #[Route('/{id}/done', name: 'app_task_done', methods: ['POST'])]
    public function done(
        Request $request,
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
            $task->setStatus(TaskStatus::PENDING);
        }

        $entityManager->flush();

        $request->getSession()->set('just_toggled_task_id', $task->getId());

        return $this->redirectToRoute('app_dashboard');
    }

    // #[Route('/{id}', name: 'app_task_show', methods: ['GET'])]
    // public function show(Task $task, #[CurrentUser] User $user): Response
    // {
    //     if ($task->getUser() !== $user) {
    //         throw $this->createAccessDeniedException("Utilisateur invalide");
    //     }

    //     return $this->render('task/show.html.twig', [
    //         'task' => $task,
    //     ]);
    // }

    // #[Route('/{id}/edit', name: 'app_task_edit', methods: ['GET', 'POST'])]
    // public function edit(
    //     Request $request,
    //     Task $task,
    //     EntityManagerInterface $entityManager,
    //     #[CurrentUser] User $user
    // ): Response {
    //     if ($task->getUser() !== $user) {
    //         throw $this->createAccessDeniedException('Utilisateur invalide');
    //     }

    //     $form = $this->createForm(TaskType::class, $task, [
    //         'user' => $user,
    //     ]);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager->flush();

    //         return $this->redirectToRoute('app_dashboard');
    //     }

    //     return $this->render('task/edit.html.twig', [
    //         'task' => $task,
    //         'form' => $form,
    //     ]);
    // }

    // #[Route('/{id}', name: 'app_task_delete', methods: ['POST'])]
    // public function delete(
    //     Request $request,
    //     Task $task,
    //     EntityManagerInterface $entityManager,
    //     #[CurrentUser] User $user
    // ): Response {
    //     if ($task->getUser() !== $user) {
    //         throw $this->createAccessDeniedException('Utilisateur invalide');
    //     }

    //     if ($this->isCsrfTokenValid('delete' . $task->getId(), $request->getPayload()->getString('_token'))) {
    //         $entityManager->remove($task);
    //         $entityManager->flush();
    //     }

    //     return $this->redirectToRoute('app_dashboard');
    // }
}
