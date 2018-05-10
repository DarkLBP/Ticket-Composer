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

    public function actionSite()
    {
        $this->renderview('site');
    }

    public function actionTickets()
    {
        $ticketsModel = $this->getModel('tickets');
        $departmentsModel = $this->getModel('departments');
        $postModel = $this->getModel('posts');
        $ticketsModel->join($departmentsModel, 'department', 'id', 'left');
        $ticketsModel->join($postModel, 'id', 'ticketId', 'inner');
        $userId = $this->request->getSessionParam('loggedUser')['id'];
        $tickets = $ticketsModel->find([
            'createdBy' => $userId
        ], [
            "$ticketsModel.id",
            "$ticketsModel.title",
            "$ticketsModel.open",
            [
                "count(*)" => "totalPosts",
                "max($postModel.created)" => "lastReply",
                "$departmentsModel.name" => "departmentName"
            ]
        ],[
            "$ticketsModel.id"
        ], [
            'lastReply' => 'desc'
        ]);
        $this->request->setViewParam('myTickets', $tickets);
        $this->renderView('tickets');
    }

    public function actionUsers()
    {
        $usersModel = $this->getModel('users');
        $users = $usersModel->find();
        $this->request->setViewParam('users', $users);
        $this->renderView('users');
    }
}