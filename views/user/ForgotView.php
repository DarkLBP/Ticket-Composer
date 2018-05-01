<?php

use Core\Utils;

if (!empty($errors)) {
    echo '<p>' . implode('<br>', $errors) . '</p>';
}
?>
<div class="centered-form">
    <h2>Forgot Password</h2>
    <form action="<?= Utils::getURL("user", "forgot") ?>" method="post">
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email"><br>
        <input type="submit" value="Recover">
    </form>
</div>