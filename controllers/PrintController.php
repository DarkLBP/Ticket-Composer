<?php
namespace Controllers;

include_once __DIR__ . "/../vendor/OverviewPDF.php";

use Core\Controller;

class PrintController extends Controller
{
    public function actionOverview()
    {
        $pdf = new \OverviewPDF();
        $attachmentsModel = $this->getModel('attachments');
        $departmentsModel = $this->getModel('departments');
        $postsModel = $this->getModel('posts');
        $recoversModel = $this->getModel('recovers');
        $sessionsModels = $this->getModel('sessions');
        $ticketsModel = $this->getModel('tickets');
        $usersModel = $this->getModel('users');
        $validationsModel = $this->getModel('validations');
        $attachments = $attachmentsModel->count();
        $departments = $departmentsModel->count();
        $posts = $postsModel->count();
        $recovers = $recoversModel->count();
        $sessions = $sessionsModels->count();
        $tickets = $ticketsModel->count();
        $users = $usersModel->count();
        $validations = $validationsModel->count();
        $pdf->PrintLine('Number of uploaded attachments: ' . $attachments);
        $pdf->PrintLine('Number of departments: ' . $departments);
        $pdf->PrintLine('Number of posts created: ' . $posts);
        $pdf->PrintLine('Number of pending account recoveries: ' . $recovers);
        $pdf->PrintLine('Number of started sessions: ' . $sessions);
        $pdf->PrintLine('Number of created tickets: ' . $tickets);
        $pdf->PrintLine('Number of registered users: ' . $users);
        $pdf->PrintLine('Number of pending validations: ' . $validations);
        $pdf->Render();
    }
}