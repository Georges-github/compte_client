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
use App\Validation\Validations;
use App\Validation\ValidationGroups;

use Symfony\Component\Form\FormError;

use App\Service\FileUploader;

use Symfony\Component\HttpFoundation\BinaryFileResponse;


#[Route('/contrat')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class ContratController extends AbstractController {

    #[ Route( '/ajouterUnContrat/{id}' , name: 'app_ajouter_un_contrat' , methods: [ 'GET' , 'POST' ] ) ]
    public function ajouterUnContrat(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader, UtilisateurRepository $utilisateurRepository) : Response
    {
        $contrat = new Contrat();

        $form = $this->createForm( EditerUnContratType::class , $contrat );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $idClient = $request->attributes->get( 'id' );

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

                $etatChoisi = $form->get('etatChoisi')->getData();

                $etatContrat = new EtatContrat();
                $etatContrat->setEtat($etatChoisi);
                $etatContrat->setDateHeureInsertion(new \DateTimeImmutable());
                $etatContrat->setIdUtilisateur($this->getUser());
                
                $contrat->addEtatContrat($etatContrat);

                $entityManager->persist($contrat);

                $entityManager->flush();
    
                return $this->redirectToRoute('app_liste_des_contrats', [ 'id' => $idClient ], Response::HTTP_SEE_OTHER);
            }

        }

        return $this->render( 'FrontEnd/EditerUnContrat.html.twig' , [ 'form' => $form, 'edition' => false ] );
    }

    #[Route('/voirUnContrat/{id}' , name: 'app_voir_un_contrat' , methods: [ 'GET' ])]
    public function voirUnContrat( Request $request , ContratRepository $contratRepository , UtilisateurRepository $utilisateurRepository , EtatContratRepository $etatContratRepository ) : Response {

        $idContrat = $request->attributes->get( 'id' );

        $contrat = $contratRepository->findOneBy( [ 'id' => $idContrat ] );

        $client = $contrat->getIdUtilisateur();

        $etatsSuccessifs = $etatContratRepository->findBy( [ 'idContrat' => $idContrat ] , ['dateHeureInsertion' => 'DESC'] );

        $etatsSuccessifsSpecifiesPar = [];
        foreach( $etatsSuccessifs as $etat ) {
            $etatsSuccessifsSpecifiesPar[] = [ 'etat' => $etat , 'specifiePar' => $etat->getIdUtilisateur() ];
        }

        return $this->render( 'FrontEnd/voirUnContrat.html.twig' , [ 'contrat' => $contrat , 'client' => $client , 'etatsSuccessifsSpecifiesPar' => $etatsSuccessifsSpecifiesPar ] );

    }

    #[Route('/contrat/pdf/{cheminFichier}', name: 'contrat_pdf',  requirements: ['cheminFichier' => '.+'])]
    public function afficherPdf(string $cheminFichier): BinaryFileResponse
    {
        // $cheminFichier = basename($cheminFichier);

        $filePath = $this->getParameter('kernel.project_dir') . '/var/storage/' . $cheminFichier;

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Fichier non trouvé');
        }

        return new BinaryFileResponse($filePath);
        
    }


}