<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Club;
use App\Entity\Inscription;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

use CMEN\GoogleChartsBundle\GoogleCharts\Charts\BarChart;



use App\Repository\InscriptionRepository;
use App\Repository\OffreRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\Recipient;


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
