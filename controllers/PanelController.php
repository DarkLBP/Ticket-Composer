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
}