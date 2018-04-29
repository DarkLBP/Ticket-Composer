<?php

namespace Controllers;

use Core\Controller;
use Core\Utils;

class DepartmentController extends Controller
{
    public function actionCreate()
    {
        if ($this->request->isPost()) {
            $name = $this->request->getPostParam('name', true);
            if (empty($name)) {
                $this->request->setViewParam('error', 'Department name is empty');
                $this->renderView('create');
            }
            $model = $this->getModel('departments');
            $model->insert([
                'name' => $name
            ]);
            $this->request->setViewParam('department', $name);
            $this->request->redirect(Utils::getURL('panel', 'departments'));
        }
        $this->renderView('create');
    }

    public function actionDelete($params = [])
    {
        if (!empty($params[0])) {
            $department = $params[0];
            $model = $this->getModel('departments');
            $exists = $model->findOne($department);
            if (!empty($exists)) {
                if ($this->request->isPost()) {
                    $model->delete(["id" => $department]);
                    $this->request->redirect(Utils::getURL('panel', 'departments'));
                }
                $name = $exists["name"];
                $this->request->setViewParam('name', $name);
                $this->request->setViewParam('department', $department);
                $this->renderView('delete');
            }
        }
        $this->renderView('invalidDepartment');
    }

    public function actionEdit($params = [])
    {
        if (!empty($params[0])) {
            $department = $params[0];
            $model = $this->getModel('departments');
            $exists = $model->findOne($department);
            if (!empty($exists)) {
                if ($this->request->isPost()) {
                    $name = $this->request->getPostParam('department', true);
                    if (empty($name)) {
                        $this->request->setViewParam('department', $exists);
                        $this->request->setViewParam('error', 'Department name is empty');
                        $this->renderView('edit');
                    }
                    $model->update(["name" => $name], ["id" => $department]);
                    $this->request->redirect(Utils::getURL('panel', 'departments'));
                }
                $this->request->setViewParam('department', $exists);
                $this->renderView('edit');
            }
        }
        $this->renderView('invalidDepartment');
    }
}