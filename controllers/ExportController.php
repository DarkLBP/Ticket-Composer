<?php

namespace Controllers;

use Core\Controller;
use Core\Request;

class ExportController extends Controller
{
    function __construct(Request $request)
    {
        parent::__construct($request);
        $loggedUser = $this->request->getSessionParam('loggedUser');
        if ($loggedUser['op'] == 0) {
            $this->renderView("forbidden");
        }
        $this->request->setResponseHeader('Content-Type', 'application/json');
    }

    function actionTickets() {
        $this->request->setResponseHeader("Content-Disposition", "attachment; filename=tickets.json");
        $ticketsModel = $this->getModel("tickets");
        echo json_encode($ticketsModel->find(), JSON_PRETTY_PRINT);
    }

    function actionPosts() {
        $this->request->setResponseHeader("Content-Disposition", "attachment; filename=posts.json");
        $postsModel = $this->getModel("posts");
        echo json_encode($postsModel->find(), JSON_PRETTY_PRINT);
    }

    function actionUsers() {
        $this->request->setResponseHeader("Content-Disposition", "attachment; filename=users.json");
        $usersModel = $this->getModel("users");
        echo json_encode($usersModel->find(), JSON_PRETTY_PRINT);
    }

    function actionAttachments() {
        $this->request->setResponseHeader("Content-Disposition", "attachment; filename=attachments.json");
        $attachmentsModel = $this->getModel("attachments");
        echo json_encode($attachmentsModel->find(), JSON_PRETTY_PRINT);
    }

    function actionDepartments() {
        $this->request->setResponseHeader("Content-Disposition", "attachment; filename=departments.json");
        $departmentsModel = $this->getModel("departments");
        echo json_encode($departmentsModel->find(), JSON_PRETTY_PRINT);
    }
}