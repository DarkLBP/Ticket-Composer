<?php

use Core\Utils;

/**
 * @var array $params
 */
if (!empty($errors)) {
    echo '<p>' . implode('<br>', $errors) . '</p>';
}
?>
<form action="<?= Utils::getURL("user", "recover", $params) ?>" method="post">
    <label for="password">New Password:</label><br>
    <input type="password" id="password" name="password"><br>
    <label for="confirm">Confirm New Password:</label><br>
    <input type="password" id="confirm" name="confirm"><br>
    <input type="submit" value="Recover">
</form>