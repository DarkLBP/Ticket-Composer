<?php

namespace Controllers;

use Core\Controller;
use Core\Request;

class ExportController extends Controller
{
    /**
     * ExportController constructor.
     * @param Request $request The incoming request
     */
    function __construct(Request $request)
    {
        parent::__construct($request);
        $loggedUser = $this->request->getSessionParam('loggedUser');
        if ($loggedUser['op'] == 0) {
            $this->renderView("forbidden");
        }
        $this->request->setResponseHeader('Content-Type', 'application/json');
    }

    /**
     * Returns a JSON array with tickets to the client
     */
    function actionTickets() {
        $this->request->setResponseHeader("Content-Disposition", "attachment; filename=tickets.json");
        $ticketsModel = $this->getModel("tickets");
        echo json_encode($ticketsModel->find(), JSON_PRETTY_PRINT);
    }

    /**
     * Returns a JSON array with posts to the client
     */
    function actionPosts() {
        $this->request->setResponseHeader("Content-Disposition", "attachment; filename=posts.json");
        $postsModel = $this->getModel("posts");
        echo json_encode($postsModel->find(), JSON_PRETTY_PRINT);
    }

    /**
     * Returns a JSON array with users to the client
     */
    function actionUsers() {
        $this->request->setResponseHeader("Content-Disposition", "attachment; filename=users.json");
        $usersModel = $this->getModel("users");
        echo json_encode($usersModel->find(), JSON_PRETTY_PRINT);
    }

    /**
     * Returns a JSON array with attachments to the client
     */
    function actionAttachments() {
        $this->request->setResponseHeader("Content-Disposition", "attachment; filename=attachments.json");
        $attachmentsModel = $this->getModel("attachments");
        echo json_encode($attachmentsModel->find(), JSON_PRETTY_PRINT);
    }

    /**
     * Returns a JSON array with departments to the client
     */
    function actionDepartments() {
        $this->request->setResponseHeader("Content-Disposition", "attachment; filename=departments.json");
        $departmentsModel = $this->getModel("departments");
        echo json_encode($departmentsModel->find(), JSON_PRETTY_PRINT);
    }
}