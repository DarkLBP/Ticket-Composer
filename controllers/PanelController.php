<?php

namespace Controllers;

use Core\Controller;

class PanelController extends Controller
{
    public function actionIndex()
    {
        $this->renderView('index');
    }

    public function actionDepartments()
    {
        $departmentsModel = $this->getModel('departments');
        $departments = $departmentsModel->find();
        $this->request->setViewParam('departments', $departments);
        $this->renderView('departments');
    }

    public function actionMyTickets()
    {
        $ticketsModel = $this->getModel('tickets');
        $departmentsModel = $this->getModel('departments');
        $ticketsModel->join($departmentsModel, 'department', 'id', 'left');
        $userId = $this->request->getSessionParam('loggedUser')['id'];
        $tickets = $ticketsModel->find([
            'createdBy' => $userId
        ], [
            "$ticketsModel.*",
            ["$departmentsModel.name", "departmentName"]
        ], [
            'created' => 'desc'
        ]);
        $this->request->setViewParam('tickets', $tickets);
        $this->renderView('myTickets');
    }
}