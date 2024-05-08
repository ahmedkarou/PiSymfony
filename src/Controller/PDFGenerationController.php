<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use App\Repository\ReclamationRepository;
use App\Entity\Reclamation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


use Dompdf\Dompdf;
use Dompdf\Options;

class PDFGenerationController extends AbstractController
{

    #[Route('/pdf', name: 'app_generate_pdf', methods: ['GET'])]
    public function generatePdfAction(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Fetch reclamations from the database
        $reclamations = $entityManager
            ->getRepository(Reclamation::class)
            ->findAll();

        // Create a Dompdf instance
        $pdfOptions = new Options();
        $pdfOptions->set('isHtml5ParserEnabled', true);
        $pdfOptions->set('isPhpEnabled', true);
        $dompdf = new Dompdf($pdfOptions);

        // Generate some HTML content for the PDF (replace with your own template)
        $html = $this->renderView('reclamation/pdf.html.twig', [
            'reclamations' => $reclamations, // Pass the data to the PDF template
        ]);

        // Load HTML content into Dompdf
        $dompdf->loadHtml($html);

        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render PDF (first, load the content into the PDF)
        $dompdf->render();

        // Generate a unique filename for the PDF
        $filename = 'document_' . uniqid() . '.pdf';

        // Save the PDF to a temporary location (you can also store it permanently)
        $output = $dompdf->output();
        file_put_contents($filename, $output);

        // Create a response with the PDF file
        $response = new Response(file_get_contents($filename));
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Pragma', 'private');
        $response->headers->set('Expires', '0');

        // Delete the temporary PDF file
        unlink($filename);

        return $response;
    }
}
