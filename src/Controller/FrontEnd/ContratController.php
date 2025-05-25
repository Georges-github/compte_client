<?php

namespace App\Controller\FrontEnd;

use App\Entity\Contrat;
use App\Entity\EtatContrat;
use App\Entity\Utilisateur;
use App\Repository\ContratRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Symfony\Component\Validator\Validator\ValidatorInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Form\FrontEnd\EditerUnContratType;
use App\Repository\EtatContratRepository;
use App\Service\ContratActif;
use App\Validation\Validations;
use App\Validation\ValidationGroups;

use Symfony\Component\Form\FormError;

use App\Service\FileUploader;
use App\Service\PileDePDFDansPublic;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[Route('/contrat')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class ContratController extends AbstractController {

    private $params;
    private $pileDePDFDansPublic;

    public function __construct(ParameterBagInterface $params , PileDePDFDansPublic $pileDePDFDansPublic)
    {
        $this->params = $params;
        $this->pileDePDFDansPublic = $pileDePDFDansPublic;
    }

    #[ Route( '/ajouterUnContrat/{id}' , name: 'app_ajouter_un_contrat' , methods: [ 'GET' , 'POST' ] ) ]
    public function ajouterUnContrat(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader, UtilisateurRepository $utilisateurRepository) : Response
    {
        $contrat = new Contrat();

        $form = $this->createForm( EditerUnContratType::class , $contrat , [ 'edition' => false ] );

        $form->handleRequest($request);

        $idClient = $request->attributes->get( 'id' );


        if ($form->isSubmitted() && $form->isValid()) {

            $client = $utilisateurRepository->findOneBy( [ 'id' => $idClient ] );

            $contrat->setIdUtilisateur( $client );

            $contrat->setDateHeureInsertion( new \DateTimeImmutable( 'now', new \DateTimeZone('Europe/Paris') ) );

            $uploadedFile = $form->get('cheminFichier')->getData();

            if ($uploadedFile) {
                $filename = $fileUploader->upload(
                    $uploadedFile,
                    $client->getId(),
                    'contrat',
                    $contrat->getCheminFichier(),
                    false,
                    [300, 300]
                );

                $contrat->setCheminFichier( $filename );
            }

            $etatChoisi = $form->get('etatChoisi')->getData();

            $etatContrat = new EtatContrat();
            $etatContrat->setEtat($etatChoisi);
            $etatContrat->setDateHeureInsertion(new \DateTimeImmutable());
            $etatContrat->setIdUtilisateur($this->getUser());
            
            $contrat->addEtatContrat($etatContrat);

            $entityManager->persist($contrat);

            $entityManager->flush();

            return $this->redirectToRoute('app_liste_des_contrats', [ 'id' => $idClient , 'pathContratDansPublic' => '' ], Response::HTTP_SEE_OTHER);
        }

        return $this->render( 'FrontEnd/EditerUnContrat.html.twig' , [ 'form' => $form, 'edition' => false, 'id' => $idClient ] );
    }

    #[Route('/voirUnContrat/{id}' , name: 'app_voir_un_contrat' , methods: [ 'GET' ])]
    public function voirUnContrat( Request $request ,
                                    ContratRepository $contratRepository ,
                                    UtilisateurRepository $utilisateurRepository ,
                                    EtatContratRepository $etatContratRepository ,
                                    ContratActif $contratActif) : Response {

        $idContrat = $request->attributes->get( 'id' );

        $contrat = $contratRepository->findOneBy( [ 'id' => $idContrat ] );

        $client = $contrat->getIdUtilisateur();

        $etatsSuccessifs = $etatContratRepository->findBy( [ 'idContrat' => $idContrat ] , ['dateHeureInsertion' => 'DESC'] );

        $etatsSuccessifsSpecifiesPar = [];
        foreach( $etatsSuccessifs as $etat ) {
            $etatsSuccessifsSpecifiesPar[] = [ 'etat' => $etat , 'specifiePar' => $etat->getIdUtilisateur() ];
        }

        $pathinfo = pathinfo( $contrat->getCheminFichier() );
        $this->copyPdfToPublic( $pathinfo['dirname'] , $pathinfo['basename']);

        $contratActif->set( $contrat );

        return $this->render( 'FrontEnd/voirUnContrat.html.twig' , [
            'contrat' => $contrat ,
            'client' => $client ,
            'employe' => ( $this->getUser() )->sesRolesContiennent( 'EMPLOYE' ) ,
            'etatsSuccessifsSpecifiesPar' => $etatsSuccessifsSpecifiesPar ] );

    }

    public function copyPdfToPublic(string $path , string $filename): string
    {
        $filesystem = new Filesystem();

        $p = str_replace( '\\', '/', $this->getParameter('kernel.project_dir') );

        $sourcePath = $p . '/var/storage/' . $path . '/' . $filename;
        $targetDir = $p . '/public/storage/' . $path . '/';
        $targetPath = $targetDir . $filename;

        if (!file_exists($sourcePath)) {
            throw new \RuntimeException("Le fichier source n'existe pas : $sourcePath");
        }

        if (!$filesystem->exists($targetDir)) {
            $filesystem->mkdir($targetDir, 0755);
        }

        try {
            $filesystem->copy($sourcePath, $targetPath, true); // true = overwrite
        } catch (IOExceptionInterface $exception) {
            throw new \RuntimeException("Erreur lors de la copie du fichier : " . $exception->getMessage());
        }

        $this->pileDePDFDansPublic->push( $targetPath );

        return $targetPath;
    }

//     #[Route('/pdf/{cheminFichier}', name: 'contrat_pdf',  requirements: ['cheminFichier' => '.+'])]
//     public function afficherPdf(string $cheminFichier): BinaryFileResponse
//     {
//         // $cheminFichier = basename($cheminFichier);
// file_put_contents('var/log/trace.txt', "appelé avec : $cheminFichier");

//         $filePath = $this->getParameter('private_upload_dir') . $cheminFichier;

//         if (!file_exists($filePath)) {
//             throw $this->createNotFoundException('Fichier non trouvé');
//         }

//         return new BinaryFileResponse($filePath);
        
//     }

    #[ Route( '/editerUnContrat/{id}' , name: 'app_editer_un_contrat' , methods: [ 'GET' , 'POST' ] ) ]
    public function editerUnContrat(Request $request,
                                    EntityManagerInterface $entityManager,
                                    FileUploader $fileUploader,
                                    ContratRepository $contratRepository) : Response
    {
        // $contrat = $contratRepository->findOneBy( [ 'id' => $request->attributes->get( 'id' ) ] );

        $contrat = $contratRepository->findWithEtats( $request->attributes->get( 'id' ) );

        $client = $contrat->getIdUtilisateur();

        $form = $this->createForm( EditerUnContratType::class , $contrat ,
        [ 'etatActuel' => $contrat->getDernierEtat() !== null ? $contrat->getDernierEtat()->getEtat() : null ,
        'nomContratActuel' => basename( $contrat->getCheminFichier() ) ,
        'edition' => true ] );

        $form->handleRequest($request);

        $pathContratActuel = pathinfo( $contrat->getCheminFichier() );
        $pathContratActuelDansPublic = $this->copyPdfToPublic( $pathContratActuel['dirname'] , $pathContratActuel['basename']);

        if ($form->isSubmitted() && $form->isValid()) {

            $uploadedFile = $form->get('cheminFichier')->getData();

            $f = $this->pileDePDFDansPublic->peek();
            if ( $f ) {
                unlink( $f );
                $this->pileDePDFDansPublic->pop();
            }
            if ( $uploadedFile ) {

                $filename = $fileUploader->upload(
                    $uploadedFile,
                    $client->getId(),
                    'contrat',
                    $contrat->getCheminFichier(),
                    false,
                    [300, 300]
                );

                $contrat->setCheminFichier( $filename );

                if ( $f ) {
                    $f = str_replace( "/public/" , "/var/" , $f );
                    if ( file_exists( $f ) ) {
                        unlink( $f );
                    }
                }
            }

            $etatChoisi = $form->get('etatChoisi')->getData();

            if ( strcmp( $etatChoisi , $contrat->getDernierEtat()->getEtat() ) != 0 ) {
                $etatContrat = new EtatContrat();
                $etatContrat->setEtat($etatChoisi);
                $etatContrat->setDateHeureInsertion(new \DateTimeImmutable());
                $etatContrat->setIdUtilisateur($this->getUser());

                $contrat->addEtatContrat($etatContrat);
            }

            $contrat->setDateHeureMAJ( new \DateTimeImmutable( 'now', new \DateTimeZone('Europe/Paris') ) );

            $entityManager->persist($contrat);

            $entityManager->flush();

            // if ( file_exists( $pathContratActuelDansPublic ) ) {
            //     unlink( $pathContratActuelDansPublic );
            // }

            // $pathContratActuelDansVar = str_replace( "/public/" , "/var/" , $pathContratActuelDansPublic );
            // if ( file_exists( $pathContratActuelDansVar ) ) {
            //     unlink( $pathContratActuelDansVar );
            // }

            return $this->redirectToRoute('', [ 'id' => $client->getId() , 'pathContratDansPublic' => '' ] , Response::HTTP_SEE_OTHER);

        }

        return $this->render( 'FrontEnd/EditerUnContrat.html.twig' , [ 'form' => $form,
        'edition' => false ,
        'pathContratActuel' => 'storage/' . $pathContratActuel['dirname'] . '/' . $pathContratActuel['basename'] ,
        'id' => $client->getId() ] );
    }

    #[ Route( '/supprimerUnContrat/{id}' , name: 'app_supprimer_un_contrat' , methods: [ 'POST' ] ) ]
    public function delete(Request $request, Contrat $contrat, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('supprimer_un_contrat_'.$contrat->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($contrat);
            $entityManager->flush();
        }

        // $pathContratActuelDansPublic = str_replace( "//" , "/" , $this->params->get('app.public_upload_dir') . $contrat->getCheminFichier() );
        // if ( file_exists( $pathContratActuelDansPublic ) ) {
        //     unlink( $pathContratActuelDansPublic );
        // }

        $pathContratActuelDansPrivate = str_replace( "//" , "/" , $this->params->get('app.private_upload_dir') . $contrat->getCheminFichier() );
        if ( file_exists( $pathContratActuelDansPrivate ) ) {
            unlink( $pathContratActuelDansPrivate );
        }

        $client = $contrat->getIdUtilisateur();

        return $this->redirectToRoute('', [ 'id' => $client->getId() , 'pathContratDansPublic' => '' ] , Response::HTTP_SEE_OTHER);
    }


}