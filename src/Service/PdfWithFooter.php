<?php

namespace App\Service;

use FPDF;

class PdfWithFooter extends FPDF
{
    public function Footer(): void
    {
        // Position à 15mm du bas
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(120);
        $this->Cell(0, 10, utf8_decode('Page ') . $this->PageNo() . ' / {nb}', 0, 0, 'C');
    }

    public function Header(): void
    {
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(50, 50, 50);
        $this->Cell(0, 10, utf8_decode("Fiche d'avancement de chantier"), 0, 1, 'C');
        $this->Ln(5);
    }
}
