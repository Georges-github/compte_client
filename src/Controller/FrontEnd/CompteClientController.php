<?php

namespace App\Controller\FrontEnd;

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

use App\Form\FrontEnd\EditerUnCompteClientType;
use App\Service\PileDePDFDansPublic;
use App\Validation\Validations;
use App\Validation\ValidationGroups;

use Symfony\Component\Form\FormError;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


#[Route('/client')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class CompteClientController extends AbstractController {

    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    #[Route('/listeDesContrats/{id}' , name: 'app_liste_des_contrats', methods: ['GET'])]
    public function listeDesContrats(Request $request, ContratRepository $contratRepository, UtilisateurRepository $utilisateurRepository , PileDePDFDansPublic $pileDePDFDansPublic) : Response {

        dump($pileDePDFDansPublic);

        $f = $pileDePDFDansPublic->peek();
        if ( $f ) {
            unlink( $f );
            $pileDePDFDansPublic->pop();
        }

        // if ( ! empty( $request->query->get( 'pathContratDansPublic' ) ) ) {
        //     $pathContratActuelDansPublic = str_replace( "//" , "/" , $this->params->get('app.public_upload_dir') . $request->query->get( 'pathContratDansPublic' ) );
        //     $pathContratActuelDansPublic = str_replace( "/storage/" , "/" , $pathContratActuelDansPublic );
        //     if ( file_exists( $pathContratActuelDansPublic ) ) {
        //         unlink( $pathContratActuelDansPublic );
        //     }
        // }

        $id = $request->attributes->get( 'id' );

        $client = $utilisateurRepository->findOneBy( [ 'id' => $id ] );

        // $listeDesContrats = $contratRepository->createQueryBuilder( 'c' )
        // ->andWhere( 'c.idUtilisateur = :idUtilisateur' )
        // ->setParameter( 'idUtilisateur' , $id )
        // ->getQuery()
        // ->getResult();

        $listeDesContrats = $contratRepository->createQueryBuilder('c')
            ->leftJoin('c.etatsContrat', 'e')
            ->addSelect('e')
            ->andWhere('c.idUtilisateur = :idUtilisateur')
            ->setParameter('idUtilisateur', $id)
            ->getQuery()
            ->getResult();

            /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();

        return $this->render( 'FrontEnd/listeDesContrats.html.twig' , [ 'employeOuAdministrateur' => $utilisateur->sesRolesContiennent( 'EMPLOYE' ), 'client' => $client, 'listeDesContrats' => $listeDesContrats ] );

    }

    #[ Route( '/editerUnCompteClient/{id}' , name: 'app_editer_un_compte_client' , methods: [ 'GET' , 'POST' ] ) ]
    public function editerUnCompteClient(Request $request,
                                    UtilisateurRepository $utilisateurRepository,
                                    EntityManagerInterface $entityManager,
                                    UserPasswordHasherInterface $userPasswordHasher,
                                    ValidatorInterface $validator): Response
    {
        $Client = $utilisateurRepository->findOneBy( [ 'id' => $request->attributes->get( 'id' ) ] );

        $form = $this->createForm(EditerUnCompteClientType::class, $Client, ['validation_groups' => ['Default'] , 'edition' => true]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $pwd = $form->get('plainPassword')->getData();
            if ( $pwd != null ) {

                $erreurs = Validations::validerMotDePasse( $pwd , $validator );
                if (!empty($erreurs)) {
                    foreach ($erreurs as $message) {
                        $form->get('plainPassword')->addError(new FormError($message));
                    }
            
                    return $this->render('FrontEnd/editerUnCompteClient.html.twig', [
                        'Client' => $Client,
                        'form' => $form
                    ]);
                }

                $pwd = $userPasswordHasher->hashPassword( $Client , $pwd );
                $Client->setPassword( $pwd );
            }                
                
            $Client->setDateHeureMAJ( new \DateTimeImmutable( 'now', new \DateTimeZone('Europe/Paris') ) );
            
            $entityManager->flush();

            return $this->redirectToRoute('app_liste_des_comptes_clients', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render( 'FrontEnd/EditerUnCompteClient.html.twig' , [
            'Client' => $Client ,
            'form' => $form ,
            'edition' => true
        ]);

    }

    #[ Route( '/ajouterUnCompteClient' , name: 'app_ajouter_un_compte_client' , methods: [ 'GET' , 'POST' ] ) ]
    public function ajouterUnCompteClient(Request $request, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $Client = new Utilisateur();

        $form = $this->createForm(EditerUnCompteClientType::class, $Client, ['validation_groups' => ['Default' , ValidationGroups::AJOUTER_UN_CLIENT] , 'edition' => false]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $pwd = $form->get('plainPassword')->getData();
            if ( $pwd != null ) {
                $pwd = $userPasswordHasher->hashPassword( $Client , $pwd );
                $Client->setPassword( $pwd );
            }

            $Client->setDateHeureInsertion( new \DateTimeImmutable( 'now', new \DateTimeZone('Europe/Paris') ) );

            $entityManager->persist($Client);

            $entityManager->flush();

            return $this->redirectToRoute('app_liste_des_comptes_clients', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render( 'FrontEnd/EditerUnCompteClient.html.twig' , [
            'Client' => $Client ,
            'form' => $form ,
            'edition' => false
        ]);

    }

}