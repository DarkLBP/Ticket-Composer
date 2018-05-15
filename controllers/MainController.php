<?php

namespace Controllers;

use Core\Controller;

class MainController extends Controller
{
    /**
     * Shows homepage
     */
    public function actionIndex()
    {
        $this->renderView('index');
    }

    /**
     * Shows an error
     */
    public function actionError()
    {
        $this->renderView('error');
    }
}