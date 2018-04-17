<?php
namespace Controllers;

use Core\Controller;
use Models\DepartmentsModel;

class TicketsController extends Controller
{
    public function actionIndex()
    {
        echo "Here should appear a list of tickets if there are any";
    }

    public function actionCreate()
    {
        if ($this->request->isGet()) {
            $departmentModel = new DepartmentsModel();
            $departments = $departmentModel->find();
            $this->request->setViewParam('departments', $departments);
            $this->renderView('create');
        }
    }
}