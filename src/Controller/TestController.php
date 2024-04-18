<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Club;
use App\Entity\Event;
use App\Entity\Participatient;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;


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
    #[Route('/frontevent', name: 'app_event_front')]
    public function showFrontEvent(EntityManagerInterface $entityManager): Response
    {
        $eventRepository = $entityManager->getRepository(Event::class);
        $events = $eventRepository->findAll();
        
        return $this->render('test/showeventfront.html.twig', [
            'events' => $events,
            
        ]);
        
    }
    #[Route('/subscribe', name: 'subscribe')]
    public function subscribe(Request $request,EntityManagerInterface $entityManager)
    {
        // Create a new Participant entity
        $participant = new Participatient();

        // Create form
        $form = $this->createFormBuilder($participant)
            ->add('nom_par')
            ->add('prenom_par')
            ->add('age_par')
            
            ->getForm();

        // Handle form submission
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Retrieve event ID from form
            $eventId = $form->get('event_id')->getData();

            // Find the Event entity by ID
         

                $eventRepository = $entityManager->getRepository(Event::class);
                $event = $eventRepository->find($eventId);

            if (!$event) {
                throw $this->createNotFoundException('Event not found');
            }

            // Set Event for Participant
            $participant->setEvent($event);

            // Persist Participant entity
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($participant);
            $entityManager->flush();

            // Redirect or do something else after successful submission
            return $this->redirectToRoute('app_event_front');
        }

        // Render form in template
        return $this->render('test/showeventfront.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

