<?php

namespace Controllers;

use Core\Controller;
use Models\DepartmentsModel;

class DepartmentsController extends Controller
{
    public function actionIndex()
    {
        echo "Here should appear a list of tickets if there are any";
    }

    public function actionCreate()
    {
        if ($this->request->isGet()) {
            $this->renderView('create');
        } else {
            $name = $this->request->getPostParam('name', true);
            if (empty($name)) {
                $this->request->setViewParam('error', 'Department name is empty');
                $this->renderView('create');
            } else {
                $model = new DepartmentsModel();
                $model->insert([
                    'name' => $name
                ]);
                $this->request->setViewParam('department', $name);
                $this->renderView('created');
            }
        }
    }
}