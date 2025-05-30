<?php

namespace App\Controller\BackEnd\Administrateur;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;

use App\Form\BackEnd\Administrateur\EditerUnEmployeType;

use Symfony\Component\Form\FormError;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Attribute\Route;

use App\Validation\ValidationGroups;
use App\Validation\Validations;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Symfony\Component\Validator\Validator\ValidatorInterface;

#[ Route( '/administrateur' ) ]
#[ IsGranted( 'IS_AUTHENTICATED_FULLY' ) ]
#[ IsGranted( 'ROLE_EMPLOYE_ADMINISTRATEUR' ) ]
final class AdministrateurController extends AbstractController
{

    // public const EDITER_EMPLOYE = 'EditerUnEmploye';

    #[ Route( name: 'app_accueil_administrateur' , methods: [ 'GET' ] ) ]
    public function accueilAdministrateur(Request $request): Response
    {

        $request->getSession()->set( 'sujet' , 'Accueil' );

        return $this->render( 'BackEnd/Administrateur/accueilAdministrateur.html.twig' , [
            'utilisateur' => $this->getUser() ,
        ] );

    }

    #[ Route( '/listeDesEmployes' , name: 'app_liste_des_employes' , methods: [ 'GET' ] ) ]
    public function listeDesEmployes( UtilisateurRepository $utilisateurRepository , Request $request ) : Response
    {

        $request->getSession()->set( 'sujet' , 'Employes' );

        $listeDesEmployes = $utilisateurRepository->createQueryBuilder( 'u' )
            ->andWhere( 'u.roles LIKE :e' )
            ->setParameter( 'e' , '%EMPLOYE%' )
            ->getQuery()
            ->getResult();

        return $this->render( 'BackEnd/Administrateur/listeDesEmployes.html.twig' , [
            'listeDesEmployes' => $listeDesEmployes
        ] );

    }

    #[ Route( '/voirUnEmploye/{id}' , name: 'app_voir_un_employe' , methods: [ 'GET' ] ) ]
    public function voirUnEmploye( UtilisateurRepository $utilisateurRepository , Request $request ) : Response
    {

        $employe = $utilisateurRepository->findOneBy( [ 'id' => $request->attributes->get( 'id' ) ] );

        return $this->render( 'BackEnd/Administrateur/voirUnEmploye.html.twig' , [
            'employe' => $employe
        ]);

    }

    #[ Route( '/editerUnEmploye/{id}' , name: 'app_editer_un_employe' , methods: [ 'GET' , 'POST' ] ) ]
    public function editerUnEmploye(Request $request,
                                    UtilisateurRepository $utilisateurRepository,
                                    EntityManagerInterface $entityManager,
                                    UserPasswordHasherInterface $userPasswordHasher,
                                    ValidatorInterface $validator): Response
    {
        $employe = $utilisateurRepository->findOneBy( [ 'id' => $request->attributes->get( 'id' ) ] );

        $form = $this->createForm(EditerUnEmployeType::class, $employe, ['validation_groups' => ['Default'] , 'edition' => true]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $pwd = $form->get('plainPassword')->getData();
            if ( $pwd != null ) {

                $erreurs = Validations::validerMotDePasse( $pwd , $validator );
                if (!empty($erreurs)) {
                    foreach ($erreurs as $message) {
                        $form->get('plainPassword')->addError(new FormError($message));
                    }
            
                    return $this->render('BackEnd/Administrateur/editerUnEmploye.html.twig', [
                        'employe' => $employe,
                        'form' => $form
                    ]);
                }

                $pwd = $userPasswordHasher->hashPassword( $employe , $pwd );
                $employe->setPassword( $pwd );
            }                
                
            $employe->setDateHeureMAJ( new \DateTimeImmutable( 'now', new \DateTimeZone('Europe/Paris') ) );
            
            $entityManager->flush();

            return $this->redirectToRoute('app_liste_des_employes', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render( 'BackEnd/Administrateur/editerUnEmploye.html.twig' , [
            'employe' => $employe ,
            'form' => $form ,
            'edition' => true
        ]);

    }

    #[ Route( '/ajouterUnEmploye' , name: 'app_ajouter_un_employe' , methods: [ 'GET' , 'POST' ] ) ]
    public function ajouterUnEmploye(Request $request, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $employe = new Utilisateur();

        $form = $this->createForm(EditerUnEmployeType::class, $employe, ['validation_groups' => ['Default' , ValidationGroups::AJOUTER_UN_EMPLOYE] , 'edition' => false]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $pwd = $form->get('plainPassword')->getData();
            if ( $pwd != null ) {
                $pwd = $userPasswordHasher->hashPassword( $employe , $pwd );
                $employe->setPassword( $pwd );
            }

            $employe->setDateHeureInsertion( new \DateTimeImmutable( 'now', new \DateTimeZone('Europe/Paris') ) );

            $entityManager->persist($employe);

            $entityManager->flush();

            return $this->redirectToRoute('app_liste_des_employes', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render( 'BackEnd/Administrateur/EditerUnEmploye.html.twig' , [
            'employe' => $employe ,
            'form' => $form ,
            'edition' => false
        ]);

    }

}
