<?php

use Core\Utils;

/**
 * @var string $controller
 * @var string $action
 * @var array $loggedUser
 */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="keywords" content="<?= SITE_TITLE ?>, ticket, composer, support">
    <meta name="copyright" content="Ticket Composer is a script made by Leandro Botella https://github.com/DarkLBP">
    <meta name="description"
          content="<?= SITE_TITLE ?> ticket management site. Enter and we will manage your concerns.">
    <title><?= SITE_TITLE ?></title>
    <link rel="stylesheet" href="<?= Utils::getURL() ?>/css/style.css">
    <script src="<?= Utils::getURL() ?>/js/EasyMVC.js"></script>
    <script src="<?= Utils::getURL() ?>/js/jquery-3.3.1.js"></script>
    <script src="<?= Utils::getURL() ?>/js/Chart.js"></script>
    <script>
        //PHP injected script
        <?= "const serverTimezoneOffset = " . (date('Z') / 60) . ";" ?>
    </script>
    <script src="<?= Utils::getURL() ?>/js/script.js"></script>
</head>
<body>
<header id="main-header">
    <h1 class="title">
        <a href="<?= Utils::getURL() ?>"><?= SITE_TITLE ?></a>
    </h1>
</header>
<nav id="main-nav">
    <input type="checkbox" id="hamburger">
    <label for="hamburger">&#9776; Menu</label>
    <ul class="navigation">
        <?php
        if (!empty($loggedUser)) {
            echo "<li" . ($action == 'index' && $controller == 'panel' ? ' class="active"' : '') . "><a href='" . Utils::getURL('panel') . "'>Home</a></li>";
            echo "<li" . ($action == 'tickets' && $controller == 'panel' ? ' class="active"' : '') . "><a href='" . Utils::getURL('panel', 'tickets') . "'>Tickets</a></li>";
            if ($loggedUser['op'] == 1) {
                echo "<li" . ($action == 'departments' && $controller == 'panel' ? ' class="active"' : '') . "><a href='" . Utils::getURL('panel', 'departments') . "'>Departments</a></li>";
                echo "<li" . ($action == 'users' && $controller == 'panel' ? ' class="active"' : '') . "><a href='" . Utils::getURL('panel', 'users') . "'>Users</a></li>";
                echo "<li" . ($action == 'site' && $controller == 'panel' ? ' class="active"' : '') . "><a href='" . Utils::getURL('panel', 'site') . "'>Site</a></li>";
            }
            echo "<li class='user-box'><span>$loggedUser[name] $loggedUser[surname]</span>";
            echo '<ul>';
            echo "<li" . ($action == 'edit' && $controller == 'user' ? ' class="active"' : '') . "><a href='" . Utils::getURL('user', 'edit') . "'>Edit Details</a></li>";
            echo "<li><a href='" . Utils::getURL('user', 'logout') . "'>Logout</a></li>";
            echo '</ul>';
            echo '</li>';
        } else if ($controller !== 'install') {
            echo "<li" . ($action == 'login' && $controller == 'user' ? ' class="active"' : '') . "><a href='" . Utils::getURL('user', 'login') . "'>Login</a></li>";
            echo "<li" . ($action == 'register' && $controller == 'user' ? ' class="active"' : '') . "><a href='" . Utils::getURL('user', 'register') . "'>Register</a></li>";
        }
        ?>
    </ul>
</nav>
<main id="main-content">