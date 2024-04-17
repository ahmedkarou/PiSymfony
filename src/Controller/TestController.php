<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Club;


class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(): Response
    {
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    #[Route('/front', name: 'app_show_front')]
    public function showClubFront(EntityManagerInterface $entityManager): Response
    {
        $clubRepository = $entityManager->getRepository(Club::class);
        $clubs = $clubRepository->findAll();

        return $this->render('test/showclub.html.twig', [
            'clubs' => $clubs,
        ]);
    }
}
