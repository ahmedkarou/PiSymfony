<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use App\Entity\Event;
use App\Entity\Commande;

class QrCodeGeneratorService extends AbstractController
{
    #[Route('/qr-code/event/{id}', name: 'app_event_qr_code')]
    public function generateEventQrCode(Event $event): Response
    {
        $writer = new PngWriter();

        // Construct the content of the QR Code for Event
        $content = "C'est quoi le  {$event->gettype()}\n";

        $qrCode = QrCode::create($content)
            ->setSize(300)
            ->setMargin(10);

        $qrCodeImage = $writer->write($qrCode)->getDataUri();

        return $this->render('event/qrcode.html.twig', [
            'qrCodeImage' => $qrCodeImage,
            'event' => $event, 
        ]);
    }

}
