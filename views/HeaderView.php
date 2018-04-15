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
</header>
<nav id="main-nav">
    <?php
    /**
     * @var bool $loggedIn
     * @var string $controller
     * @var string $action
     */
    if ($controller !== 'user' || $action !== 'login' && $action !== 'register') {
        if ($loggedIn){
            echo "<a href='" . Utils::getURL('user', 'logout') . "'>Logout</a>";
        } else {
            echo "<a href='" . Utils::getURL('user', 'login') . "'>Log In</a>";
            echo "<a href='" . Utils::getURL('user', 'register') . "'>Register</a>";
        }
    }
    ?>
</nav>