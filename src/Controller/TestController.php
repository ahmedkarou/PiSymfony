<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Club;
use App\Entity\Event;
use App\Entity\Participatient;
use App\Entity\Rating;
use App\Entity\Inscription;
use App\Repository\RatingRepository;
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
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;





use App\Repository\InscriptionRepository;
use App\Repository\OffreRepository;

use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\Recipient;



class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(PaginatorInterface $paginator): Response
    {
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
           
        ])
        ;
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
    public function showFrontEvent(EntityManagerInterface $entityManager,RatingRepository $ratingRepository): Response
    {
        $eventRepository = $entityManager->getRepository(Event::class);
        $events = $eventRepository->findAll();
        $averageRatings = [];
        foreach ($events as $event) {
            $averageRatings[$event->getId()] = $ratingRepository->getAverageRatingForEvent($event->getId());
        }
        
        return $this->render('test/showeventfront.html.twig', [
            'events' => $events,
            'averageRatings' => $averageRatings,
            
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
        if ($event->getCapacite() === 0) {
            $this->addFlash('error', "Capacity of the event is zero");
            return $this->redirectToRoute('app_event_front');
           // throw $this->createNotFoundException('Capacity of the event is null');
          
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

            $event->setCapacite($event->getCapacite() - 1);
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
#[Route('/addRating', name: 'add_rating', methods: ['POST'])]
public function addRating(Request $request, EntityManagerInterface $entityManager, SessionInterface $session,LoggerInterface $logger): JsonResponse
{
    $logger->info('Received request: ' . $request);
    // Get data from the AJAX request
    $jsonData = json_decode($request->getContent(), true);

    // Check if JSON data is valid and contains required fields
    if (!isset($jsonData['eventId'], $jsonData['ratingValue'])) {
        return new JsonResponse(['success' => false, 'message' => 'Invalid JSON data'], JsonResponse::HTTP_BAD_REQUEST);
    }

    // Retrieve eventId and ratingValue from JSON data
    $eventId = $jsonData['eventId'];
    $ratingValue = $jsonData['ratingValue'];
    $sessionId = $session->getId();
    $logger->info('Received event ID: ' . $eventId);

    // Retrieve the event entity
    $event = $entityManager->getRepository(Event::class)->find($eventId);
 

    // Check if the event exists
    if (!$event) {
        return new JsonResponse(['success' => false, 'message' => 'Event not found'], Response::HTTP_NOT_FOUND);
    }

    // Create a new Rating entity
    $rating = new Rating();
    $rating->setValue($ratingValue);
    $rating->setEvent($event);
    $rating->setUser($sessionId);

    // Persist and flush the rating entity
    $entityManager->persist($rating);
    $entityManager->flush();

    // Return a JSON response indicating success
    return new JsonResponse(['success' => true]);
}

#[Route('/getOfferById/{id}', name: 'getOfferById',methods: ['GET'])]
public function findByIdClub( $id, OffreRepository $offreRepository)
{
    $offers = $offreRepository->findByClubId($id);

    // Prepare the offers for JSON response
    $formattedOffers = [];
    foreach ($offers as $offer) {
        $formattedOffers[] = [
            'id' => $offer->getId(),
            'description' => $offer->getDescription(),
        ];
    }

    return new JsonResponse(['offer' => $formattedOffers]);
}
#[Route('/notify', name: 'notify')]
public function notify(HubInterface $hub, LoggerInterface $logger): Response
{
    $logger->info('Notify endpoint called'); // Log a message to indicate that the endpoint was called

    // Publish the update to the Mercure hub
    $update = new Update(
        'https://example.com/notifications',
        json_encode(['message' => 'Button clicked!'])
    );
    try {
        $hub->publish($update);
        $logger->info('Update published successfully');
    } catch (\Throwable $e) {
        $logger->error('Failed to publish update: ' . $e->getMessage());
    }

    // Render the HTML content with the button
    return $this->render('test/index1.html.twig');
}

#[Route('/createInscription', name: 'createInscription', methods: ['POST'])]
public function createInscription(Request $request, EntityManagerInterface $entityManager, OffreRepository $offreRepository,LoggerInterface $logger,HubInterface $hub,MailerInterface $mailer): Response
{
$logger->info('Received request: ' . $request);

$clubId = $request->request->get('clubId');
$firstName = $request->request->get('firstName');
$lastName = $request->request->get('lastName');
$email = $request->request->get('email');
$phone = $request->request->get('phone');
//////////////////////////
$jsonData = json_decode($request->getContent(), true);

// Check if JSON data is valid and contains required fields
if (!isset($jsonData['clubId'], $jsonData['firstName'],$jsonData['lastName'], $jsonData['email'], $jsonData['phone'])) {
    return new JsonResponse(['success' => false, 'message' => 'Invalid JSON data'], JsonResponse::HTTP_BAD_REQUEST);
}

// Retrieve eventId and ratingValue from JSON data
$clubId=$jsonData['clubId'];
$firstName=$jsonData['firstName'];
$lastName=$jsonData['lastName'];
$email=$jsonData['email'];
$phone=$jsonData['phone'];








////////////////////////////////


$club = $entityManager->getRepository(Club::class)->find($clubId);


if (!$club ) {
    return new Response('Club or Offer not found', Response::HTTP_NOT_FOUND);
}

if ($club->getCapacity() > 0) {
    $inscription = new Inscription();
    $inscription->setFirstName($firstName);
    $inscription->setLastName($lastName);
    $inscription->setEmail($email);
    $inscription->setPhone($phone);
    $inscription->setClub($club);

    $entityManager->persist($inscription);
    $entityManager->flush();

    // Decrease the capacity of the club by 1
    $club->setCapacity($club->getCapacity() - 1);
    $entityManager->flush();

    $email = (new Email())
        ->from('your@example.com')
        ->to($email)
        ->subject('New Inscription')
        ->html('<p>A new inscription has been created</p>');

    $mailer->send($email);

     $update = new Update(
         'https://example.com/clubs',
         json_encode(['message' => 'A new subscriber'])
     );
     $hub->publish($update);

    return new JsonResponse(['success' => true, 'message' => 'Inscription created'], Response::HTTP_CREATED);
} else {
    return new JsonResponse(['success' => false, 'message' => 'Club is already at full capacity'], Response::HTTP_BAD_REQUEST);
}
}

#[Route('/stat', name: 'club_stat')]
public function show(InscriptionRepository $inscriptionRepository): Response
{
     $stats = $inscriptionRepository->getInscriptionsCountPerClub();
     return $this->render('test/stat.html.twig', [
         'stats' => $stats
     ]);
    
}
}

