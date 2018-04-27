<?php
session_start();
require_once __DIR__ . '/../core/Config.php';
$request = new \Core\Request();
$session = new \Controllers\SessionController($request);
$session->initialize();
$request->setViewParam('loggedUser', $request->getSessionParam('loggedUser'));
$request->setViewParam('controller', $request->getController());
$request->setViewParam('action', $request->getAction());
$request->dispatch();