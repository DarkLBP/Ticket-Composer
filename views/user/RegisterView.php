<?php

use Core\Utils;

if (!empty($errors)) {
    echo '<p>' . implode('<br>', $errors) . '</p>';
}
?>
<form method="post" action="<?= Utils::getURL("user", "register") ?>">
    <label for="name">Name:</label><br>
    <input type="text" id="name" name="name"><br>
    <label for="surname">Surname:</label><br>
    <input type="text" id="surname" name="surname"><br>
    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email"><br>
    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password"><br>
    <label for="confirm">Confirm Password:</label><br>
    <input type="password" id="confirm" name="confirm"><br>
    <input type="submit" value="Register">
</form>