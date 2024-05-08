<?php

namespace App\Controller;

use App\Service\SmsService;
use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Symfony\Component\Mailer\MailerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Mime\Email;
use DateTime; 

class ReclamationController extends AbstractController
{
    #[Route('/listerec', name: 'app_reclamation_index_front', methods: ['GET'])]
    public function index(ReclamationRepository $reclamationRepository): Response
    {
        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamationRepository->findAll(),
        ]);
    }
    #[Route('back/listerec', name: 'app_reclamation_index_back', methods: ['GET'])]
    public function index2(ReclamationRepository $reclamationRepository): Response
    {
        return $this->render('reclamation/index2.html.twig', [
            'reclamations' => $reclamationRepository->findAll(),
        ]);
    }


    private function sendEmail(MailerInterface $mailer): void
    {
        $email = (new Email())
            ->from('kharrat.raed@esprit.tn')
            ->to('medamine.kbaier@esprit.tn')
            ->subject('Reclamation')
            ->text('Sending emails is fun again!')
            ->html('<p>Hello amine, Reclamation ajoutée.</p><p>Thanks.</p>');

        $mailer->send($email);
    }

    
    #[Route('/reclamation', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer, SmsService $smsService): Response
{
    // Créer une nouvelle réclamation
    $reclamation = new Reclamation();
    $reclamation->setDateRec(new \DateTime());

    // Créer le formulaire de réclamation
    $form = $this->createForm(ReclamationType::class, $reclamation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Récupérer les mots inappropriés à partir du fichier
        $badWords = $this->getBadWordsFromFile('C:\Users\ideap\Desktop\baggage\kbaier\kbaier\stop.txt');

        // Filtrer le message de réclamation
        $filteredMessage = $this->filterBadWords($reclamation->getMessage(), $badWords);
        $reclamation->setMessage($filteredMessage);

        // Persister la réclamation en base de données
        $entityManager->persist($reclamation);
        $entityManager->flush();

        // Envoyer une notification par e-mail
        $this->sendEmail($mailer);

        // Envoyer une notification par SMS
        $smsRecipient = '+21698501786'; // Numéro de téléphone du destinataire
        $smsMessage = 'Nouvelle réclamation créée : ' . $filteredMessage; // Message filtré
        $smsService->sendSms($smsRecipient, $smsMessage);

        // Rediriger vers la page d'accueil ou une autre page après la création de la réclamation
        return $this->redirectToRoute('app_reclamation_index_front', [], Response::HTTP_SEE_OTHER);
    }

    // Afficher le formulaire de réclamation
    return $this->renderForm('reclamation/_form.html.twig', [
        'reclamation' => $reclamation,
        'form' => $form,
    ]);
}


private function getBadWordsFromFile(string $filePath): array
{
    $badWords = [];

    // Vérifier si le fichier existe et le lire ligne par ligne
    if (file_exists($filePath)) {
        $fileContent = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $badWords = array_map('trim', $fileContent); // Supprimer les espaces autour des mots
    }

    return $badWords;
}

private function filterBadWords(string $message, array $badWords): string
{
    foreach ($badWords as $word) {
        $message = preg_replace('/\b' . preg_quote($word, '/') . '\b/i', str_repeat('*', strlen($word)), $message);
    }

    return $message;
}


    #[Route('/{id}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }
   

    #[Route('/{id}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reclamation_index_front', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reclamation_index_front', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/reclamation/{id}/traite', name: 'reclamation_traite', methods: ['POST'])]
    public function traiteReclamation(Request $request, Reclamation $reclamation): Response
    {
        // Set the 'statut' to "traite"
        $reclamation->setStatut('traite');
        
        // Persist the changes to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        
        // Redirect back to the page or return a response
        return $this->redirectToRoute('app_reclamation_index_back',['id' => $reclamation->getId()]);

    }
}
