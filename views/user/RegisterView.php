<?php
use Core\Utils;
?>
<div class="centered-form">
    <h2>Create New Account</h2>
    <?= !empty($errors) ? '<p class="error-message">' . implode('<br>', $errors) . '</p>' : '' ?>
    <form method="post" action="<?= Utils::getURL("user", "register") ?>">
        <label for="name">Name</label><br>
        <input type="text" id="name" name="name" required><br>
        <label for="surname">Surname</label><br>
        <input type="text" id="surname" name="surname" required><br>
        <label for="email">Email</label><br>
        <input type="email" id="email" name="email" required><br>
        <label for="password">Password</label><br>
        <input type="password" id="password" name="password" required><br>
        <label for="confirm">Confirm Password</label><br>
        <input type="password" id="confirm" name="confirm" required><br>
        <input type="submit" value="Register">
    </form>
</div>