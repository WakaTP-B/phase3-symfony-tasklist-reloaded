<?php

namespace App\Controller;

use App\Entity\Priority;
use App\Entity\User;
use App\Repository\PriorityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/priority')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class PriorityController extends AbstractController
{
    #[Route('', name: 'app_priority_index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        PriorityRepository $priorityRepository,
        EntityManagerInterface $entityManager,
        #[CurrentUser] User $user
    ): Response {

        if ($request->isMethod('POST')) {
            $name = trim($request->request->get('name', ''));

            if ($name !== '') {
                $priority = new Priority();
                $priority->setName($name);
                $priority->setColor('#e5e7eb');
                $priority->setUser($user);

                $entityManager->persist($priority);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_priority_index');
        }

        $priorities = $priorityRepository->findAvailableForUser($user);

        return $this->render('priority/index.html.twig', [
            'priorities' => $priorities,
        ]);
    }
}