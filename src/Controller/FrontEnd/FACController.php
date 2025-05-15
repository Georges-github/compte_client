<?php

namespace App\Controller\FrontEnd;

use App\Entity\Publication;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;


use App\Form\FrontEnd\EditerUnePublicationType;
use App\Repository\PublicationRepository;
use App\Service\ContratActif;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Routing\Attribute\Route;

#[Route('/fac')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class FACController extends AbstractController {

    // public function __construct() {}

    #[Route('/fac' , name: 'app_afficher_fac' , methods: ['GET'])]
    public function afficherFAC(Request $request) : Response
    {
        return $this->render( 'FrontEnd/afficherFAC.html.twig' );
    }

    #[Route('/ajouterUnePublication' , name: 'app_ajouter_une_publication' , methods: ['POST'])]
    public function ajouterUnePublication(Request $request,
                                        EntityManagerInterface $entityManager,
                                        PublicationRepository $publicationRepository,
                                        FileUploader $fileUploader,
                                        ContratActif $contratActif) : Response
    {
        $publication = new Publication();

        $form = $this->createForm( EditerUnePublicationType::class , $publication , [ 'edition' => false ] );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $utilisateur = $this->getUser();

            $publication->setIdUtilisateur( $utilisateur );

            $publication->setDateHeureInsertion( new \DateTimeImmutable( 'now', new \DateTimeZone('Europe/Paris') ) );

            // $photo = $form->get('cheminFichierImage')->getData();
            // if ( $photo ) {
            //     $filename = $fileUploader->upload(
            //         $photo,
            //         $utilisateur->getId(),
            //         'photo',
            //         $publication->getChemin(),
            //         false,
            //         [300, 300]
            //     );

            //     $contrat->setCheminFichier( $filename );
            // }

            $contrat = $contratActif->get();

            $publication->setIdContrat( $contrat );

            $entityManager->persist($publication);

            $entityManager->flush();

            return $this->redirectToRoute('app_afficher_fac', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render( 'FrontEnd/EditerUnePublication.html.twig' , [ 'form' => $form, 'edition' => false ] );
    }
}