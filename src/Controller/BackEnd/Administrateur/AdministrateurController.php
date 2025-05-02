<?php

namespace App\Controller\BackEnd\Administrateur;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use App\Form\EditerUnEmployeType;
use Doctrine\ORM\EntityManager;

#[ Route( '/administrateur' ) ]
#[ IsGranted( 'IS_AUTHENTICATED_FULLY' ) ]
#[ IsGranted( 'ROLE_EMPLOYE_ADMINISTRATEUR' ) ]
final class AdministrateurController extends AbstractController
{

    #[ Route( name: 'app_accueil_administrateur' , methods: [ 'GET' ] ) ]
    public function accueilAdministrateur(): Response
    {

        return $this->render( 'BackEnd/Administrateur/accueilAdministrateur.html.twig' , [
            'utilisateur' => $this->getUser() ,
        ] );

    }

    #[ Route( '/listeDesEmployes' , name: 'app_liste_des_employes' , methods: [ 'GET' ] ) ]
    public function listeDesEmployes( UtilisateurRepository $utilisateurRepository ) : Response
    {

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
    public function editerUnEmploye(Request $request, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $employe = $utilisateurRepository->findOneBy( [ 'id' => $request->attributes->get( 'id' ) ] );

        $form = $this->createForm(EditerUnEmployeType::class, $employe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $pwd = $form->get('plainPassword')->getData();
            $pwd = $userPasswordHasher->hashPassword( $employe , $pwd );
            $employe->setPassword( $pwd );


            $employe->setDateHeureMAJ( new \DateTimeImmutable( 'now', new \DateTimeZone('Europe/Paris') ) );

            $entityManager->flush();

            return $this->redirectToRoute('app_voir_un_employe', [ 'id' => $employe->getId() ], Response::HTTP_SEE_OTHER);
        }

        return $this->render( 'BackEnd/Administrateur/editerUnEmploye.html.twig' , [
            'employe' => $employe ,
            'form' => $form
        ]);

    }

}
