<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Attribute\Route;

use App\Entity\EtatContrat;

use App\Repository\EtatContratRepository;

use App\Form\EtatContratType;

#[Route('/etatcontrat')]
final class EtatContratController extends AbstractController
{
    #[Route(name: 'app_etat_contrat_index', methods: ['GET'])]
    public function index(EtatContratRepository $etatContratRepository): Response
    {
        return $this->render('etat_contrat/index.html.twig', [
            'etat_contrats' => $etatContratRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_etat_contrat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $etatContrat = new EtatContrat();
        $form = $this->createForm(EtatContratType::class, $etatContrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $etatContrat->setDateHeureInsertion( new \DateTimeImmutable( 'now', new \DateTimeZone('Europe/Paris') ) );

            $entityManager->persist($etatContrat);
            $entityManager->flush();

            return $this->redirectToRoute('app_etat_contrat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('etat_contrat/new.html.twig', [
            'etat_contrat' => $etatContrat,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_etat_contrat_show', methods: ['GET'])]
    public function show(EtatContrat $etatContrat): Response
    {
        return $this->render('etat_contrat/show.html.twig', [
            'etat_contrat' => $etatContrat,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_etat_contrat_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EtatContrat $etatContrat, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EtatContratType::class, $etatContrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $etatContrat->setDateHeureMAJ( new \DateTimeImmutable( 'now', new \DateTimeZone('Europe/Paris') ) );

            $entityManager->flush();

            return $this->redirectToRoute('app_etat_contrat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('etat_contrat/edit.html.twig', [
            'etat_contrat' => $etatContrat,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_etat_contrat_delete', methods: ['POST'])]
    public function delete(Request $request, EtatContrat $etatContrat, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$etatContrat->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($etatContrat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_etat_contrat_index', [], Response::HTTP_SEE_OTHER);
    }
}
