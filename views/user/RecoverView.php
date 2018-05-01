<?php
use Core\Utils;
/**
 * @var array $params
 */
?>
<div class="centered-form">
    <h2>Recover Account</h2>
    <?= !empty($errors) ? '<p class="error-message">' . implode('<br>', $errors) . '</p>' : '' ?>
    <form action="<?= Utils::getURL("user", "recover", $params) ?>" method="post">
        <label for="password">New Password</label><br>
        <input type="password" id="password" name="password"><br>
        <label for="confirm">Confirm New Password</label><br>
        <input type="password" id="confirm" name="confirm"><br>
        <input type="submit" value="Recover">
    </form>
</div>