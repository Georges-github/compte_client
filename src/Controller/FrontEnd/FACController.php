<?php

namespace App\Controller\FrontEnd;

use App\Entity\Photo;
use App\Entity\Publication;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;

use App\Form\FrontEnd\NouveauAjouterUnePublicationType;

use App\Form\FrontEnd\UniqueAjouterUnePublicationType;
use App\Form\FrontEnd\UniqueEditerUnePublicationType;

use App\Form\FrontEnd\EditerUnePublicationType;
use App\Form\FrontEnd\AjouterUnePublicationType;
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

    #[Route('/uniqueAjouterUnePublication' , name: 'app_unique_ajouter_une_publication' , methods: ['POST'])]
    public function uniqueAjouterUnePublication(Request $request,
                                        EntityManagerInterface $entityManager,
                                        FileUploader $fileUploader,
                                        ContratActif $contratActif) : Response
    {
        $publication = new Publication();

        $form = $this->createForm( UniqueAjouterUnePublicationType::class , $publication );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $utilisateur = $this->getUser();

            $publication->setIdUtilisateur( $utilisateur );

            $publication->setDateHeureInsertion( new \DateTimeImmutable( 'now', new \DateTimeZone('Europe/Paris') ) );

            $contrat = $contratActif->get();

            $publication->setIdContrat( $contrat );

            $fichierPhoto = $form->get('photo')->getData();

            if ( $fichierPhoto ) {

                $photo = new Photo();

                $cheminFichier = $fileUploader->upload(
                    $fichierPhoto,
                    $utilisateur->getId(),
                    'image',
                    'pipo',
                    false,
                    [300, 300]
                );

                $photo->setCheminFichierImage( $cheminFichier );

                $legende = $form->get('legende')->getData();

                if ( $legende ) {

                        $photo->setLegende( $legende );

                }

                $photo->setIdPublication( $publication );

                $photo->setDateHeureInsertion( new \DateTimeImmutable( 'now', new \DateTimeZone('Europe/Paris') ) );

                $entityManager->persist( $photo );

            }

            $entityManager->persist($publication);

            $entityManager->flush();

            return $this->redirectToRoute('app_afficher_fac', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render( 'FrontEnd/UniqueAjouterUnePublication.html.twig' , [ 'form' => $form ] );
    }

    #[Route('/uniqueEditerUnePublication' , name: 'app_unique_editer_une_publication' , methods: ['POST'])]
    public function uniqueEditerUnePublication(Request $request,
                                        EntityManagerInterface $entityManager,
                                        PublicationRepository $publicationRepository,
                                        FileUploader $fileUploader,
                                        ContratActif $contratActif) : Response
    {
        // $publication = $publicationRepository->find( $request->query->get( 'id' ) );

        $publication = $publicationRepository->createQueryBuilder('p')
        ->leftJoin('p.photos', 'ph')
        ->addSelect('ph')
        ->where('p.id = :id')
        ->setParameter('id', $request->query->get( 'id' ))
        ->getQuery()
        ->getOneOrNullResult();

        $form = $this->createForm( UniqueEditerUnePublicationType::class , $publication );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $utilisateur = $this->getUser();

            $publication->setIdUtilisateur( $utilisateur );

            $publication->setDateHeureInsertion( new \DateTimeImmutable( 'now', new \DateTimeZone('Europe/Paris') ) );

            $contrat = $contratActif->get();

            $publication->setIdContrat( $contrat );

            $fichierPhoto = $form->get('photo')->getData();

            if ( $fichierPhoto ) {

                $photo = new Photo();

                $cheminFichier = $fileUploader->upload(
                    $fichierPhoto,
                    $utilisateur->getId(),
                    'image',
                    'pipo',
                    false,
                    [300, 300]
                );

                $photo->setCheminFichierImage( $cheminFichier );

                $legende = $form->get('legende')->getData();

                if ( $legende ) {

                        $photo->setLegende( $legende );

                }

                $photo->setIdPublication( $publication );

                $photo->setDateHeureInsertion( new \DateTimeImmutable( 'now', new \DateTimeZone('Europe/Paris') ) );

                $entityManager->persist( $photo );

            }

            $entityManager->persist($publication);

            $entityManager->flush();

            return $this->redirectToRoute('app_afficher_fac', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render( 'FrontEnd/UniqueEditerUnePublication.html.twig' , [ 'form' => $form, 'publication' => $publication ] );
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

        $form = $this->createForm( NouveauAjouterUnePublicationType::class , $publication , [ 'edition' => false ] );

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

        return $this->render( 'FrontEnd/AjouterUnePublication.html.twig' , [ 'form' => $form, 'edition' => false ] );
    }

    #[Route('/nouveauAjouterUnePublication' , name: 'app_nouveau_ajouter_une_publication' , methods: ['POST'])]
    public function nouveauAjouterUnePublication(Request $request,
                                        EntityManagerInterface $entityManager,
                                        PublicationRepository $publicationRepository,
                                        FileUploader $fileUploader,
                                        ContratActif $contratActif,
                                        Tracer $tracer) : Response
    {
        $publication = new Publication();

        $form = $this->createForm( NouveauAjouterUnePublicationType::class , $publication , [ 'edition' => false ] );

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

        return $this->render( 'FrontEnd/NouveauAjouterUnePublication.html.twig' , [ 'form' => $form, 'edition' => false ] );
    }


    #[Route('/editerUnePublication' , name: 'app_editer_une_publication' , methods: ['POST'])]
    public function editerUnePublication(Request $request,
                                        EntityManagerInterface $entityManager,
                                        PublicationRepository $publicationRepository,
                                        FileUploader $fileUploader,
                                        ContratActif $contratActif,
                                        Tracer $tracer) : Response
    {
        $publication = $publicationRepository->find( $request->query->get( 'id' ) );

        foreach ($publication->getPhotos() as $photo) {
            if (!$entityManager->getRepository(Photo::class)->find($photo->getId())) {
                $publication->removePhoto($photo); // Doctrine Collection -> nettoyée
            }
        }
    
        $form = $this->createForm( EditerUnePublicationType::class , $publication , [ 'edition' => true ] );

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

        return $this->render( 'FrontEnd/EditerUnePublication.html.twig' , [ 'form' => $form, 'photos' => $publication->getPhotos() , 'edition' => true ] );
    }

}