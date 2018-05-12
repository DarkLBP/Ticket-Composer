<?php
include_once __DIR__ . "/../vendor/TablePDF.php";

use Core\Controller;

class PrintController extends Controller
{
    public function actionTickets()
    {
        $loggedUser = $this->request->getSessionParam('loggedUser');
        $ticketsModel = $this->getModel('tickets');
        $ticketsModel->find([
            'createdBy' => $loggedUser['id']
        ]);

    }
}