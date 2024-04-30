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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\FormInterface;
use Twilio\Rest\Client;


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
   
    #[Route('/createParticipationFront/{id}', name: 'app_crete', methods: ['GET', 'POST'])]
    public function createParticipatient(Request $request, EntityManagerInterface $entityManager, $id)
    {
     
        // Get the Event by ID
        $event = $entityManager->getRepository(Event::class)->find($id);
    
        if (!$event) {
            throw $this->createNotFoundException('Event not found');
        }
        
    
        $participatient = new Participatient();
        $participatient->setEvent($event);
    
        $form = $this->createFormBuilder($participatient)
            ->add('nom_par', TextType::class, [
                'label' => 'Your last name',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 4, 'minMessage' => 'veuillez avoir au minimum 4 caractere']),
                    new Regex(['pattern' => '/\d/', 'match' => false, 'message' => 'Your nom cannot contain a number']),
                ],
            ])
            ->add('prenom_par', TextType::class, [
                'label' => 'Your first name',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 4, 'minMessage' => 'veuillez avoir au minimum 4 caractere']),
                    new Regex(['pattern' => '/\d/', 'match' => false, 'message' => 'Your prenom cannot contain a number']),
                ],
            ])
            ->add('age_par', TextType::class, [
                'label' => 'Your age',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Subscribe',
            ])
            ->getForm();
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($participatient);
            $entityManager->flush();
            $accountSid='AC607d8b3a02c5a1e6e90c901f296afaa3';
            $authToken='2eb67c41660e0b6c71411d8cbe41db5b';
            $twilio= new Client($accountSid,$authToken);
            $messageBody = "Vous participez à l'événement: " . $event->getName();
            $message = $twilio->messages->create('+21650362781',array( 'from'=>'+12673274003','body'=> $messageBody,));
            if ($message->sid) {
                $sms= 'SMS sent successfully.';
                $this->addFlash('success', " vous ete participée");
                
                return new JsonResponse([
                    'status' => 'success',
                    'message' => 'Participation created successfully!',
                ]);
            } else {
                $sms ='Failed to send SMS.';
            }
    
            // Return a JSON response
           
        }
    
        // If the form is not submitted or not valid, return the form view
        return $this->render('test/createParticipationFront.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
           
        ]);
    }
private function getFormErrors(FormInterface $form): array
{
    $errors = [];
    foreach ($form->getErrors(true) as $error) {
        $errors[] = $error->getMessage();
    }
    return $errors;
}
}

