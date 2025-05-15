<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PileDePDFDansPublic
{
    private const SESSION_KEY = 'pile_de_pdf_dans_public';

    private ?SessionInterface $session;

    public function __construct(RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }

    public function push(string $pdfPath): bool
    {
        $pile = $this->getPile();

        if (!in_array($pdfPath, $pile, true)) {
            array_push($pile, $pdfPath);
            $this->session->set(self::SESSION_KEY, $pile);
            return true;
        }

        return false;
    }

    public function pop(): ?string
    {
        $pile = $this->getPile();

        if (empty($pile)) {
            return null;
        }

        $pdfPath = array_pop($pile);
        $this->session->set(self::SESSION_KEY, $pile);

        return $pdfPath;
    }

    public function peek(): ?string
    {
        $pile = $this->getPile();

        return end($pile) ?: null;
    }

    public function clear(): void
    {
        $this->session->remove(self::SESSION_KEY);
    }

    public function getPile(): array
    {
        return $this->session->get(self::SESSION_KEY, []);
    }

    public function count(): int
    {
        return count($this->getPile());
    }
}
