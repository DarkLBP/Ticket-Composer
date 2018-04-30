<?php
use Core\Utils;
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
    <h1>
        <a href="<?= Utils::getURL() ?>"><?= SITE_TITLE ?></a>
    </h1>
    <div id="user-box">
        <?php
        /**
         * @var string $controller
         * @var string $action
         * @var array $loggedUser
         */
        if ($controller !== 'user') {
            if ($loggedUser){
                echo "Welcome $loggedUser[name] $loggedUser[surname]<br>";
                echo "<a href='" . Utils::getURL('user', 'logout') . "'>Logout</a>";
            } else {
                echo "<a href='" . Utils::getURL('user', 'login') . "'>Log In</a> ";
                echo "<a href='" . Utils::getURL('user', 'register') . "'>Register</a>";
            }
        }
        ?>
    </div>
</header>
<nav id="main-nav">
    <ul>
        <?php
        echo "<li><a href='" . Utils::getURL('panel', 'tickets') . "'>Tickets</a></li>";
        if ($loggedUser['op'] == 1) {
            echo "<li><a href='" . Utils::getURL('panel', 'departments') . "'>Departments</a></li>";
            echo "<li><a href='" . Utils::getURL('panel', 'users') . "'>Users</a></li>";
        }
        echo "<li><a href='" . Utils::getURL('panel', 'account') . "'>Tickets</a></li>";
        ?>
    </ul>
</nav>