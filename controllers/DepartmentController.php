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
            $error = '';
            if (empty($name)) {
                $error = 'Department name is empty';
            } else {
                $model = $this->getModel('departments');
                $exists = $model->count([
                    ['name', '=', $name]
                ]);
                if ($exists == 1) {
                    $error = 'That department already exists';
                } else {
                    $model->insert([
                        'name' => $name
                    ]);
                    $this->request->redirect(Utils::getURL('panel', 'departments'));
                }
            }
            $this->request->setViewParam('error', $error);
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
                    $model->delete([
                        ["id", '=', $department]
                    ]);
                    $this->request->redirect(Utils::getURL('panel', 'departments'));
                }
                $this->request->setViewParam('department', $exists, true);
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
                    $error = '';
                    if (empty($name)) {
                        $error = 'Department name is empty';
                    } else {
                        if ($name != $exists['name']) {
                            $usedName = $model->count([
                                ['name', '=', $name]
                            ]);
                            if ($usedName == 1) {
                                $error = 'That department already exists';
                            }
                        }
                        if (empty($error)) {
                            $model->update(["name" => $name], [
                                ["id", '=', $department]
                            ]);
                            $this->request->redirect(Utils::getURL('panel', 'departments'));
                        }
                    }
                    $this->request->setViewParam('error', $error);
                }
                $this->request->setViewParam('department', $exists, true);
                $this->renderView('edit');
            }
        }
        $this->renderView('invalidDepartment');
    }
}