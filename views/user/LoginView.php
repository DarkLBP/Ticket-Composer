<?php
use Core\Utils;
?>
<div class="centered-form">
    <h2>Log In</h2>
    <?= !empty($errors) ? '<p class="error-message">' . implode('<br>', $errors) . '</p>' : '' ?>
    <form action="<?= Utils::getURL("user", "login") ?>" method="post" class="half">
        <label for="email">Email</label><br>
        <input type="email" id="email" name="email"><br>
        <label for="password">Password</label><br>
        <input type="password" id="password" name="password"><br>
        <a href="<?= Utils::getURL('user', 'forgot') ?>">Forgot Password</a><br>
        <input type="submit" value="Login">
    </form>
</div>