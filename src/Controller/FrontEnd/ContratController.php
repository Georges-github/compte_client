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
use App\Validation\Validations;
use App\Validation\ValidationGroups;

use Symfony\Component\Form\FormError;

use App\Service\FileUploader;


#[Route('/contrat')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class ContratController extends AbstractController {

    #[ Route( '/ajouterUnContrat' , name: 'app_ajouter_un_contrat' , methods: [ 'GET' , 'POST' ] ) ]
    public function ajouterUnContrat(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader) : Response
    {
        $contrat = new Contrat();

        $form = $this->createForm( EditerUnContratType::class , $contrat );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $utilisateur = $this->getUser();

            $contrat->setIdUtilisateur($utilisateur);

            $contrat->setDateHeureInsertion( new \DateTimeImmutable( 'now', new \DateTimeZone('Europe/Paris') ) );

            $uploadedFile = $form->get('cheminFichier')->getData();
            if ($uploadedFile) {
                $filename = $fileUploader->upload(
                    $uploadedFile,
                    'profiles',
                    $contrat->getCheminFichier(),
                    true,
                    [300, 300]
                );
                // Sauvegarder le nom dans l'entité, etc.
                $contrat->setCheminFichier( $filename );

                $etatChoisi = $form->get('etatChoisi')->getData();

                $etatContrat = new EtatContrat();
                $etatContrat->setEtat($etatChoisi);
                $etatContrat->setDateHeureInsertion(new \DateTimeImmutable());
                $etatContrat->setIdUtilisateur($utilisateur); // si applicable
                
                $contrat->addEtatContrat($etatContrat); // lie aussi l'objet au contrat

                $entityManager->persist($contrat);
                $entityManager->flush();
    
                return $this->redirectToRoute('app_liste_des_contrats', [], Response::HTTP_SEE_OTHER);
                }

        }

        return $this->render( 'FrontEnd/EditerUnContrat.html.twig' , [ 'form' => $form, 'edition' => false ] );
    }

}