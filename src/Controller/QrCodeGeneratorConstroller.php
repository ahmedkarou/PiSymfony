<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use App\Entity\Event;

class QrCodeGeneratorController extends AbstractController
{
    #[Route('/qr-code/{id}', name: 'app_qr_code')]
    public function generateQrCode(Event $event): Response
    {
        $writer = new PngWriter();

        // Construct the content of the QR Code
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
