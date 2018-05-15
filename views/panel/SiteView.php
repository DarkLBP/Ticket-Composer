<?php

use Core\Utils;

?>
<div class="centered-form">
    <h2>Site Settings</h2>
    <?= !empty($errors) ? '<p class="error-message">' . implode('<br>', $errors) . '</p>' : '' ?>
    <form action="<?= Utils::getURL('panel', 'site') ?>" method="post">
        <label for="title">Title</label><br>
        <input type="text" name="title" id="title" value="<?= SITE_TITLE ?>"><br>
        <label for="siteemail">Site Email</label><br>
        <input type="email" id="siteemail" name="siteemail" value="<?= SITE_EMAIL ?>"><br>
        <input type="submit" value="Update Settings">
    </form>
    <br>
    <h3>Export Data</h3>
    <a href="<?= Utils::getURL('export', 'posts') ?>" class="button">Posts</a>
    <a href="<?= Utils::getURL('export', 'tickets') ?>" class="button">Tickets</a>
    <a href="<?= Utils::getURL('export', 'users') ?>" class="button">Users</a>
    <a href="<?= Utils::getURL('export', 'attachments') ?>" class="button">Attachments</a>
    <a href="<?= Utils::getURL('export', 'departments') ?>" class="button">Departments</a>
    <h3>Other Operations</h3>
    <a href="<?= Utils::getURL('print', 'overview') ?>" class="button" target="_blank">Generate Site Overview PDF</a>
</div>
