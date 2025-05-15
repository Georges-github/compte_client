<?php

namespace App\Service;

use App\Entity\Contrat;
use App\Repository\ContratRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class ContratActif
{
    private ContratRepository $contratRepository;
    private RequestStack $requestStack;

    public function __construct(ContratRepository $contratRepository, RequestStack $requestStack)
    {
        $this->contratRepository = $contratRepository;
        $this->requestStack = $requestStack;
    }

    public function get(): Contrat
    {
        $session = $this->requestStack->getSession();
        $id = $session->get('contrat_id');

        if (!$id) {
            throw new \RuntimeException("Aucun contrat actif en session.");
        }

        $contrat = $this->contratRepository->find($id);

        if (!$contrat) {
            throw new \RuntimeException("Le contrat actif (ID $id) n'existe pas.");
        }

        return $contrat;
    }

    public function set(Contrat $contrat): void
    {
        $this->requestStack->getSession()->set('contrat_id', $contrat->getId());
    }

    public function forget(): void
    {
        $this->requestStack->getSession()->remove('contrat_id');
    }
}
