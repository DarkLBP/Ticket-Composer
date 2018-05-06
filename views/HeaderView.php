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
    <title><?= SITE_TITLE ?></title>
    <link rel="stylesheet" href="<?= Utils::getURL() ?>/css/style.css">
    <script src="<?= Utils::getURL() ?>/js/script.js"></script>
</head>
<body>
<header id="main-header">
    <h1 class="title">
        <a href="<?= Utils::getURL() ?>"><?= SITE_TITLE ?></a>
    </h1>
</header>
<nav id="main-nav">
    <ul class="navigation">
        <?php
        if ($loggedUser) {
            echo "<li><a href='" . Utils::getURL('panel', 'tickets') . "'>Tickets</a></li>";
            if ($loggedUser['op'] == 1) {
                echo "<li><a href='" . Utils::getURL('panel', 'departments') . "'>Departments</a></li>";
                echo "<li><a href='" . Utils::getURL('panel', 'users') . "'>Users</a></li>";
            }
            echo "<li><span>$loggedUser[name] $loggedUser[surname]</span>";
            echo '<ul>';
            echo "<li><a href='" . Utils::getURL('user', 'edit') . "'>Edit Details</a></li>";
            echo "<li><a href='" . Utils::getURL('user', 'logout') . "'>Logout</a></li>";
            echo '</ul>';
            echo '</li>';
        } else {
            echo "<li><a href='" . Utils::getURL('user', 'login') . "'>Login</a></li>";
            echo "<li><a href='" . Utils::getURL('user', 'register') . "'>Register</a></li>";
        }
        ?>
    </ul>
</nav>
<main id="main-content">