<?php
use Core\Utils;
?>

<div class="centered-form">
    <h2>Install Ticket Composer</h2>
    <?= !empty($errors) ? '<p class="error-message">' .implode('<br>', $errors) . '</p>' : '' ?>
    <form action="<?= Utils::getURL('install', '') ?>" method="post">
        <label for="title">Site Title</label><br>
        <input type="text" id="title" name="title"><br>
        <label for="mhost">MySQL Host</label><br>
        <input type="text" id="mhost" name="mhost"><br>
        <label for="muser">MySQL User</label><br>
        <input type="text" id="muser" name="muser"><br>
        <label for="mpass">MySQL Password</label><br>
        <input type="password" name="mpass" id="mpass"><br>
        <label for="mdb">MySQL Database</label><br>
        <input type="text" name="mdb" id="mdb"><br>
        <input type="submit" value="Install">
    </form>
</div>