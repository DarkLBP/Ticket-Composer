<?php
session_start();

if (file_exists( __DIR__ . '/../core/Config.php')) {
    require_once __DIR__ . '/../core/Config.php';
    $request = new \Core\Request();
    $session = new \Controllers\SessionController($request);
    $session->initialize();
    $request->setViewParam('loggedUser', $request->getSessionParam('loggedUser'));
    $request->setViewParam('controller', $request->getController());
    $request->setViewParam('action', $request->getAction());
} else {
    require_once __DIR__ . '/../core/ConfigDefault.php';
    $request = new \Core\Request();
    if ($request->getController() != "install") {
        $request->redirect(\Core\Utils::getURL('install'));
    }
    $request->setViewParam('loggedUser', "");
    $request->setViewParam('controller', $request->getController());
    $request->setViewParam('action', $request->getAction());
}
$request->dispatch();