<?php

namespace App\Controller;

use App\Entity\Folder;
use App\Form\FolderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FolderController extends AbstractController
{
    #[Route('/folder/new', name: 'app_folder_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $folder = new Folder();
        $form = $this->createForm(FolderType::class, $folder);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $folder->setUser($this->getUser());
            $folder->setColor($request->request->get('color', '#FF0000'));

            $em->persist($folder);
            $em->flush();

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('folder/index.html.twig', [
            'form' => $form,
        ]);
    }
}
