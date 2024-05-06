<?php

namespace App\Controller;

use App\Entity\Club;
use App\Form\ClubType;
use App\Repository\ClubRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/club')]
class ClubController extends AbstractController
{
    #[Route('/', name: 'app_club_index', methods: ['GET'])]
    public function index(ClubRepository $clubRepository): Response
    {
        return $this->render('club/index.html.twig', [
            'clubs' => $clubRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_club_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,SluggerInterface $slugger): Response
    {
        $club = new Club();
        $form = $this->createForm(ClubType::class, $club);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //recuperer les data
            $brochureFile = $form->get('image')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $club->setImage($newFilename);
            }
            $entityManager->persist($club);
            $entityManager->flush();

            return $this->redirectToRoute('app_club_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('club/new.html.twig', [
            'club' => $club,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_club_show', methods: ['GET'])]
    public function show(Club $club): Response
    {
        return $this->render('club/show.html.twig', [
            'club' => $club,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_club_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Club $club, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ClubType::class, $club);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_club_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('club/edit.html.twig', [
            'club' => $club,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_club_delete', methods: ['POST'])]
    public function delete(Request $request, Club $club, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$club->getId(), $request->request->get('_token'))) {
            $entityManager->remove($club);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_club_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/search', name: 'club_search', methods: ['GET'])]
public function search(Request $request, ClubRepository $clubRepository,LoggerInterface $logger): Response
{
    $logger->info('Received request: ' . $request);
    $keyword = $request->query->get('keyword');

    $clubs = $clubRepository->findByKeyword($keyword);

    // Serialize clubs to array to be returned in JSON response
    $clubsArray = [];
    foreach ($clubs as $club) {
        $createdAt = $club->getCreatedAt() ? $club->getCreatedAt()->format('Y-m-d H:i:s') : null;
        $clubsArray[] = [
            'id' => $club->getId(),
            'name' => $club->getName(),
            'organizer' => $club->getOrganizer(),
            'location' => $club->getLocation(),
            'capacity' => $club->getCapacity(),
            'createdAt' => $createdAt,
            'description' => $club->getDescripton(),
            'image' => $club->getImage(),
        ];
    }

    // return new JsonResponse(['clubs' => $clubsArray]);
    return $this->render('club/show.html.twig', [
        'clubs' => $clubs,
    ]);
}

    
}
