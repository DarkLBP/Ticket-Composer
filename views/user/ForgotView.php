<?php
use Core\Utils;
?>
<div class="centered-form">
    <h2>Forgot Password</h2>
    <?= !empty($errors) ? '<p class="error-message">' . implode('<br>', $errors) . '</p>' : '' ?>
    <form action="<?= Utils::getURL("user", "forgot") ?>" method="post">
        <label for="email">Email</label><br>
        <input type="email" id="email" name="email"><br>
        <input type="submit" value="Recover Password">
    </form>
</div>