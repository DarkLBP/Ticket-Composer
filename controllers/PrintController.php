<?php
include_once __DIR__ . "/../vendor/fpdf/fpdf.php";

use Core\Controller;

class PrintController extends Controller
{
    public function actionOverview()
    {
        $loggedUser = $this->request->getSessionParam('loggedUser');
        $ticketsModel = $this->getModel('tickets');
        $ticketsModel->find([
            'createdBy' => $loggedUser['id']
        ]);

    }
}