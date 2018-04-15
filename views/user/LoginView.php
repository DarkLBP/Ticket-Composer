<?php

use Core\Utils;

if (!empty($errors)) {
    echo '<p>' . implode('<br>', $errors) . '</p>';
}
?>
<form action="<?= Utils::getURL("user", "login") ?>" method="post">
    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email"><br>
    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password"><br>
    <a href="<?= Utils::getURL("user", "register") ?>">Create an account</a><br>
    <input type="submit" value="Login">
</form>