<?php

namespace App\Controller\FrontEnd;

use App\Entity\Photo;
use App\Entity\Publication;
use App\Entity\Contrat;
use App\Entity\Commentaire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;

use App\Form\FrontEnd\NouveauAjouterUnePublicationType;

use App\Form\FrontEnd\UniqueAjouterUnePublicationType;
use App\Form\FrontEnd\UniqueEditerUnePublicationType;

use App\Form\FrontEnd\AjouterUnCommentaireType;

use App\Form\FrontEnd\EditerUnePublicationType;
use App\Form\FrontEnd\AjouterUnePublicationType;
use App\Repository\CommentaireRepository;
use App\Repository\PublicationRepository;
use App\Service\ContratActif;
use App\Service\FileUploader;
use App\Service\CommentaireTreeBuilder;
use Doctrine\ORM\EntityManagerInterface;

use FPDF;



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

        return $this->render( 'FrontEnd/afficherFAC.html.twig' , [ 'listePublications' => $listePublications , 'idContrat' => $contrat->getId() ] );
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

    private function supprimerCommentaireRecursif(Commentaire $commentaire, EntityManagerInterface $entityManager, FileUploader $fileUploader): void
    {
        // Supprimer les photos du commentaire
        foreach ($commentaire->getPhotos() as $photo) {
            $fileUploader->delete($photo->getCheminFichierImage());
            $entityManager->remove($photo);
        }

        // Supprimer les sous-commentaires (réponses)
        foreach ($commentaire->getCommentaires() as $sousCommentaire) {
            $this->supprimerCommentaireRecursif($sousCommentaire, $entityManager, $fileUploader);
        }

        $entityManager->remove($commentaire);
    }

    #[Route('/supprimerUnePublication/{id}', name: 'app_supprimer_une_publication', methods: ['POST'])]
    public function supprimerUnePublication(
        Request $request,
        EntityManagerInterface $entityManager,
        PublicationRepository $publicationRepository,
        FileUploader $fileUploader
    ): Response {
        $publication = $publicationRepository->find($request->get('id'));

        if (!$publication) {
            throw $this->createNotFoundException('Publication non trouvée.');
        }

        // Vérification du token CSRF
        if (!$this->isCsrfTokenValid('supprimer_publication_' . $publication->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        // 1. Supprimer les photos liées à la publication
        foreach ($publication->getPhotos() as $photo) {
            $fileUploader->delete($photo->getCheminFichierImage());
            $entityManager->remove($photo);
        }

        // 2. Supprimer les commentaires liés (récursivement)
        foreach ($publication->getCommentaires() as $commentaire) {
            $this->supprimerCommentaireRecursif($commentaire, $entityManager, $fileUploader);
        }

        // 3. Supprimer la publication elle-même
        $entityManager->remove($publication);
        $entityManager->flush();

        $this->addFlash('success', 'Publication et ses dépendances supprimées avec succès.');

        return $this->redirectToRoute('app_afficher_fac');
    }

    private function renderCommentaireRecursif(\FPDF $pdf, Commentaire $commentaire, FileUploader $fileUploader, int $niveau = 1): void
    {
        $indent = str_repeat('    ', $niveau);

        $pdf->SetFont('Arial', 'I', 11);
        // $pdf->MultiCell(0, 7, $indent . $commentaire->getAuteur()->getNom() . ' : ' . $commentaire->getTexte());
        $pdf->MultiCell(0, 7, $indent . ' : ' . $commentaire->getTexte());

        // Photos du commentaire
        foreach ($commentaire->getPhotos() as $photo) {
            // $imagePath = $fileUploader->getAbsolutePath($photo->getCheminFichierImage());
            $imagePath = $this->getParameter('kernel.project_dir') . '/var/storage/' . $photo->getCheminFichierImage();
            if (file_exists($imagePath)) {
                $pdf->Image($imagePath, null, null, 40); // plus petite taille
                $pdf->Ln(3);
            }
            $pdf->MultiCell(0, 8, $photo->getLegende() );
        }

        // Sous-commentaires récursifs
        foreach ($commentaire->getCommentaires() as $sousCommentaire) {
            $this->renderCommentaireRecursif($pdf, $sousCommentaire, $fileUploader, $niveau + 1);
        }

        $pdf->Ln(5);
    }

    #[Route('/genererPDFFAC/{id}', name: 'app_generer_pdf_fac')]
    public function genererPdfContrat(
        Contrat $contrat,
        FileUploader $fileUploader // pour les chemins de fichiers locaux
    ): Response {
        $pdf = new \FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        // $pdf->Cell(0, 10, 'Publications du contrat : ' . $contrat->getIntitule(), 0, 1);
        $pdf->MultiCell(0, 10, 'Publications du contrat : ' . $contrat->getIntitule(), 0, 1);

        foreach ($contrat->getPublications() as $publication) {
            $pdf->SetFont('Arial', 'B', 14);
            // $pdf->Cell(0, 10, 'Publication : ' . $publication->getTitre(), 0, 1);
            $pdf->MultiCell(0, 10, 'Publication : ' . $publication->getTitre(), 0, 1);

            $pdf->SetFont('Arial', '', 12);
            $pdf->MultiCell(0, 8, $publication->getContenu());

            // Photos de la publication
            foreach ($publication->getPhotos() as $photo) {
                // $imagePath = $fileUploader->getAbsolutePath($photo->getCheminFichierImage());
                $imagePath = $this->getParameter('kernel.project_dir') . '/var/storage/' . $photo->getCheminFichierImage();
                if (file_exists($imagePath)) {
                    $pdf->Image($imagePath, null, null, 60);
                    $pdf->Ln(5);
                }
                $pdf->MultiCell(0, 8, $photo->getLegende() );
            }

            // Commentaires (récursif)
            foreach ($publication->getCommentaires() as $commentaire) {
                $this->renderCommentaireRecursif($pdf, $commentaire, $fileUploader, 1);
            }

            $pdf->Ln(10); // espacement entre les publications
        }

        return new Response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="publications_contrat.pdf"'
        ]);
    }

    #[Route('/ajouterUnCommentaireAUnePublication/{idPublication}' , name: 'app_ajouter_un_commentaire_a_une_publication' , methods: ['POST'])]
    public function ajouterUnCommentaireAUnePublication(Request $request,
                                        EntityManagerInterface $entityManager,
                                        PublicationRepository $publicationRepository,
                                        FileUploader $fileUploader,
                                        ContratActif $contratActif) : Response
    {
        $commentaire = new Commentaire();

        $form = $this->createForm( AjouterUnCommentaireType::class , $commentaire );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $utilisateur = $this->getUser();

            $publication = $publicationRepository->find( $request->get('idPublication') );

            $commentaire->setIdPublication( $publication );

            $commentaire->setDateHeureInsertion( new \DateTimeImmutable( 'now', new \DateTimeZone('Europe/Paris') ) );

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

                $photo->setIdCommentaire( $commentaire );

                $photo->setDateHeureInsertion( new \DateTimeImmutable( 'now', new \DateTimeZone('Europe/Paris') ) );

                $entityManager->persist( $photo );

            }

            $entityManager->persist($commentaire);

            $entityManager->flush();

            return $this->redirectToRoute('app_afficher_fac', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render( 'FrontEnd/AjouterUnCommentaire.html.twig' , [ 'form' => $form ] );
    }


    #[Route('/publications/{id}', name: 'app_publications_contrat')]
    public function afficherPublicationsContrat(
        int $id,
        PublicationRepository $publicationRepository,
        CommentaireTreeBuilder $commentaireTreeBuilder
    ): Response {
        // Charge les publications avec leurs photos et commentaires (sans jointures récursives)
        $publications = $publicationRepository->findBy(['idContrat' => $id]);

        foreach ($publications as $publication) {
            // Liste plate des commentaires liés à cette publication
            $commentaires = $publication->getCommentaires()->toArray();

            // Arbre structuré à profondeur illimitée
            $publication->commentairesArbre = $commentaireTreeBuilder->buildTree($commentaires);
        }

        return $this->render('publication/liste.html.twig', [
            'publications' => $publications
        ]);
    }

    #[Route('/supprimerUnCommentaire/{id}', name: 'app_supprimer_un_commentaire', methods: ['POST'])]
    public function supprimerUnCommentaire(
        Request $request,
        EntityManagerInterface $entityManager,
        CommentaireRepository $commentaireRepository,
        FileUploader $fileUploader
    ): Response {
        $commentaire = $commentaireRepository->find($request->get('id'));

        if (!$commentaire) {
            throw $this->createNotFoundException('Publication non trouvée.');
        }

        if (!$this->isCsrfTokenValid('supprimer_un_commentaire_' . $commentaire->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        foreach ($commentaire->getPhotos() as $photo) {
            $fileUploader->delete($photo->getCheminFichierImage());
            $entityManager->remove($photo);
        }

        foreach ($commentaire->getCommentaires() as $commentaire) {
            $this->supprimerCommentaireRecursif($commentaire, $entityManager, $fileUploader);
        }

        $entityManager->remove($commentaire);
        $entityManager->flush();

        $this->addFlash('success', 'Commentaire et ses dépendances supprimées avec succès.');

        return $this->redirectToRoute('app_afficher_fac');
    }

    #[Route('/ajouterUnCommentaireAUnCommentaire/{idCommentaire}' , name: 'app_ajouter_un_commentaire_a_un_commentaire' , methods: ['POST'])]
    public function ajouterUnCommentaireAUnCommentaire(Request $request,
                                        EntityManagerInterface $entityManager,
                                        CommentaireRepository $commentaireRepository,
                                        PublicationRepository $publicationRepository,
                                        FileUploader $fileUploader,
                                        ContratActif $contratActif) : Response
    {
        $commentaire = new Commentaire();

        $form = $this->createForm( AjouterUnCommentaireType::class , $commentaire );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $utilisateur = $this->getUser();

            $commentaireParent = $commentaireRepository->find( $request->get('idCommentaire') );

            $commentaire->setIdCommentaireParent( $commentaireParent );

            $commentaire->setIdPublication( $publicationRepository->find( $request->get('idPublication') ) );

            $commentaire->setDateHeureInsertion( new \DateTimeImmutable( 'now', new \DateTimeZone('Europe/Paris') ) );

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

                $photo->setIdCommentaire( $commentaire );

                $photo->setDateHeureInsertion( new \DateTimeImmutable( 'now', new \DateTimeZone('Europe/Paris') ) );

                $entityManager->persist( $photo );

            }

            $entityManager->persist($commentaire);

            $entityManager->flush();

            return $this->redirectToRoute('app_afficher_fac', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render( 'FrontEnd/AjouterUnCommentaire.html.twig' , [ 'form' => $form ] );
    }




// ==================================================================

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