<?php

namespace Controllers;

use Core\Controller;
use Core\Utils;
use Models\DepartmentsModel;

class DepartmentsController extends Controller
{
    public function actionIndex()
    {
        echo "Here should appear a list of tickets if there are any";
    }

    public function actionCreate($params = [])
    {
        if ($this->request->isPost()) {
            $name = $this->request->getPostParam('name', true);
            if (empty($name)) {
                $this->request->setViewParam('error', 'Department name is empty');
                $this->renderView('create');
            }
            $model = new DepartmentsModel();
            $model->insert([
                'name' => $name
            ]);
            $this->request->setViewParam('department', $name);
            $this->request->setSessionParam('completed', true);
            $this->request->redirect(Utils::getURL('departments', 'create', ['completed']));
        }
        if (!empty($params[0]) && $params[0] === "completed" ) {
            if ($this->request->getSessionParam('completed')) {
                $this->request->setSessionParam('completed', false);
                $this->renderView('created');
                return;
            } else {
                $this->request->redirect(Utils::getURL('departments', 'create'));
            }
        }
        $this->renderView('create');
    }

    public function actionDelete($params = [])
    {
        if (!empty($params[0])) {
            if (!empty($params[1]) && $params[1] === "completed" ) {
                if ($this->request->getSessionParam('completed')) {
                    $this->request->setSessionParam('completed', false);
                    $this->renderView('deleted');
                }
                $this->request->redirect(Utils::getURL('departments'));
            }
            $department = $params[0];
            $model = new DepartmentsModel();
            $exists = $model->findOne($department);
            if (!empty($exists)) {
                if ($this->request->isGet()) {
                    $name = $exists["name"];
                    $this->request->setViewParam('name', $name);
                    $this->request->setViewParam('department', $department);
                    $this->renderView('deleteConfirm');
                }
                $model->delete(["id" => $department]);
                $this->request->setSessionParam('completed', true);
                $this->request->redirect(Utils::getURL('departments', 'delete', [$department, 'completed']));
            }
        }
        $this->renderView('invalidDepartment');
    }

    public function actionEdit($params = [])
    {
        if (!empty($params[0])) {
            if (!empty($params[1]) && $params[1] === "completed" ) {
                if ($this->request->getSessionParam('completed')) {
                    $this->request->setSessionParam('completed', false);
                    $this->renderView('edited');
                }
                $this->request->redirect(Utils::getURL('departments'));
            }
            $department = $params[0];
            $model = new DepartmentsModel();
            $exists = $model->findOne($department);
            if (!empty($exists)) {
                if ($this->request->isGet()) {
                    $this->request->setViewParam('name', $exists["name"]);
                    $this->request->setViewParam('department', $department);
                    $this->renderView('edit');
                }
                $name = $this->request->getPostParam('department', true);
                if (empty($name)) {
                    $this->request->setViewParam('name', $exists["name"]);
                    $this->request->setViewParam('department', $department);
                    $this->request->setViewParam('error', 'Department name is empty');
                    $this->renderView('edit');
                }
                $model->update(["name" => $name], ["id" => $department]);
                $this->request->setSessionParam('completed', true);
                $this->request->redirect(Utils::getURL('departments', 'edit', [$department, 'completed']));
            }
        }
        $this->renderView('invalidDepartment');
    }
}