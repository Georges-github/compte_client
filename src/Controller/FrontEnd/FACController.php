<?php

namespace App\Controller\FrontEnd;

use App\Entity\Photo;
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

use App\Utils\Tracer;

use Symfony\Component\Routing\Attribute\Route;

#[Route('/fac')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class FACController extends AbstractController {

    // public function __construct() {}

    #[Route('/fac' , name: 'app_afficher_fac' , methods: ['GET'])]
    public function afficherFAC(Request $request , ContratActif $contratActif , PublicationRepository $publicationRepository) : Response
    {
        $contrat = $contratActif->get();

        $listePublications = $publicationRepository->findBy( [ 'idContrat' => $contrat->getId() ] );

        return $this->render( 'FrontEnd/afficherFAC.html.twig' , [ 'listePublications' => $listePublications ] );
    }

    #[Route('/ajouterUnePublication' , name: 'app_ajouter_une_publication' , methods: ['POST'])]
    public function ajouterUnePublication(Request $request,
                                        EntityManagerInterface $entityManager,
                                        PublicationRepository $publicationRepository,
                                        FileUploader $fileUploader,
                                        ContratActif $contratActif,
                                        Tracer $tracer) : Response
    {
        $publication = new Publication();

        $form = $this->createForm( EditerUnePublicationType::class , $publication , [ 'edition' => false ] );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $utilisateur = $this->getUser();

            $publication->setIdUtilisateur( $utilisateur );

            $publication->setDateHeureInsertion( new \DateTimeImmutable( 'now', new \DateTimeZone('Europe/Paris') ) );

            $contrat = $contratActif->get();

            $publication->setIdContrat( $contrat );

            $photos = $publication->getPhotos();
            reset($photos);
            $photo = current($photos)[ 0 ];
// dump($form->get('photos'));
// dump($photos);
// dump($photo);
// $photo = $photos->next();
// dd($photo);
            foreach ($form->get('photos') as $key => $photoForm) {

                $img = $photoForm->get('imageFile')->getData();

                if ( $img ) {
                    $filename = $fileUploader->upload(
                        $img,
                        $utilisateur->getId(),
                        'image',
                        'pipo',
                        false,
                        [300, 300]
                    );
                    $photo->setCheminFichierImage( $filename );

                    $photo->setDateHeureInsertion( new \DateTimeImmutable( 'now', new \DateTimeZone('Europe/Paris') ) );

                    $entityManager->persist( $photo );

                }
                
                $photo = $photos->next();
            }

            $entityManager->persist($publication);

            $entityManager->flush();

            return $this->redirectToRoute('app_afficher_fac', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render( 'FrontEnd/EditerUnePublication.html.twig' , [ 'form' => $form, 'edition' => false ] );
    }
}