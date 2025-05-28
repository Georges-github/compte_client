<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\Photo;

class PhotoController extends AbstractController
{
    #[Route('/photo/delete/{id}', name: 'photo_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Photo $photo,
        EntityManagerInterface $em
    ): JsonResponse {
        $token = $request->headers->get('X-CSRF-TOKEN');

        if (!$this->isCsrfTokenValid('delete-photo-' . $photo->getId(), $token)) {
            return new JsonResponse(['success' => false, 'error' => 'Token CSRF invalide'], Response::HTTP_FORBIDDEN);
        }

        $chemin = $photo->getCheminFichierImage();
        $fullPath = $this->getParameter('kernel.project_dir') . '/var/storage/' . $chemin;

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        $em->remove($photo);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }
}
