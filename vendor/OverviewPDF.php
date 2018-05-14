<?php
include_once __DIR__ . "/../vendor/fpdf/fpdf.php";

class OverviewPDF extends FPDF
{

    public function __construct(string $orientation = 'P', string $unit = 'mm', string $size = 'A4')
    {
        parent::__construct($orientation, $unit, $size);
        $this->AddPage();
    }

    public function Header()
    {
        $this->SetFont('Helvetica', '', 50);
        $this->SetTextColor(78, 110, 93);
        $this->MultiCell(0, 15, SITE_TITLE, 0, 'C');
        $this->Ln(5);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Helvetica', '', 25);
        $this->MultiCell(0, 10, "Overview", 0, 'C');
        $this->Ln(15);
        $this->SetFont('Arial', '', 14);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Helvetica','I',12);
        $this->Cell(0,10,'Copyright (C) ' . date('Y') . ' ' . SITE_TITLE,0,0,'C');
    }

    public function PrintLine($text)
    {
        $this->MultiCell(0, 8, $text);
        $this->Ln(2);
    }

    public function Render()
    {
        $this->Output();
    }
}