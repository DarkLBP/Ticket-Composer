<?php

namespace Controllers;

use Core\Controller;

class PanelController extends Controller
{
    public function actionIndex()
    {
        $this->renderView('index');
    }
}