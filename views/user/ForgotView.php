<?php

use Core\Utils;

if (!empty($errors)) {
    echo '<p>' . implode('<br>', $errors) . '</p>';
}
?>
<form action="<?= Utils::getURL("user", "forgot") ?>" method="post">
    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email"><br>
    <input type="submit" value="Recover">
</form>