<?php
namespace Controllers;

include_once __DIR__ . "/../vendor/OverviewPDF.php";

use Core\Controller;

class PrintController extends Controller
{
    public function actionOverview()
    {
        $pdf = new \OverviewPDF();
        $pdf->Render();
    }
}