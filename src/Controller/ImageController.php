<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImageController extends AbstractController
{
    #[Route('/image/utilisateurs/{filename}', name: 'serve_image', requirements: ['filename' => '.+'])]
    public function serve(string $filename): Response
    {
        // dd($filename);
        // Sécuriser le filename pour éviter les chemins dangereux
        // Retirer les séquences ../ qui permettent de sortir du dossier
        $safeFilename = str_replace('..', '', $filename);

        // Construire le chemin complet dans var/storage/
        $storagePath = $this->getParameter('kernel.project_dir') . '/var/storage/' . $safeFilename;

        // Vérifier que le fichier existe
        if (!file_exists($storagePath)) {
            throw $this->createNotFoundException('Image non trouvée');
        }

        $justFilename = basename($safeFilename);

        // Retourner le fichier avec un header pour l'afficher dans le navigateur
        return (new BinaryFileResponse($storagePath))
            ->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $justFilename);
    }
}
