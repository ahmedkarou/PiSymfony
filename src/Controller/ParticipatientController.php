<?php

namespace App\Controller;

use App\Entity\Participatient;
use App\Form\ParticipatientType;
use App\Repository\ParticipatientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/participatient')]
class ParticipatientController extends AbstractController
{
    #[Route('/', name: 'app_participatient_index', methods: ['GET'])]
    public function index(ParticipatientRepository $participatientRepository): Response
    {
        return $this->render('participatient/index.html.twig', [
            'participatients' => $participatientRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_participatient_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $participatient = new Participatient();
        $form = $this->createForm(ParticipatientType::class, $participatient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($participatient);
            $entityManager->flush();

            return $this->redirectToRoute('app_participatient_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('participatient/new.html.twig', [
            'participatient' => $participatient,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_participatient_show', methods: ['GET'])]
    public function show(Participatient $participatient): Response
    {
        return $this->render('participatient/show.html.twig', [
            'participatient' => $participatient,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_participatient_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Participatient $participatient, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ParticipatientType::class, $participatient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_participatient_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('participatient/edit.html.twig', [
            'participatient' => $participatient,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_participatient_delete', methods: ['POST'])]
    public function delete(Request $request, Participatient $participatient, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participatient->getId(), $request->request->get('_token'))) {
            $entityManager->remove($participatient);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_participatient_index', [], Response::HTTP_SEE_OTHER);
    }
}
