<?php

use Core\Utils;

?>
<div class="centered-form">
    <h2>Install Ticket Composer</h2>
    <?= !empty($errors) ? '<p class="error-message">' . implode('<br>', $errors) . '</p>' : '' ?>
    <form action="<?= Utils::getURL('install', '') ?>" method="post">
        <fieldset>
            <legend>Site Settings</legend>
            <label for="title">Site Title</label><br>
            <input type="text" id="title" name="title" required><br>
            <label for="siteemail">Site Email</label><br>
            <input type="text" id="siteemail" name="siteemail" required><br>
            <label for="mhost">MySQL Host</label><br>
            <input type="text" id="mhost" name="mhost" required><br>
            <label for="muser">MySQL User</label><br>
            <input type="text" id="muser" name="muser" required><br>
            <label for="mpass">MySQL Password</label><br>
            <input type="password" name="mpass" id="mpass" required><br>
            <label for="mdb">MySQL Database</label><br>
            <input type="text" name="mdb" id="mdb" required><br>
        </fieldset>
        <fieldset>
            <legend>Admin Settings</legend>
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
        </fieldset>
        <input type="submit" value="Install Site">
    </form>
</div>