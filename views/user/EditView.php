<?php
use Core\Utils;

/**
 * @var array $user
 */
?>
<div class="centered-form">
    <h2>Edit User</h2>
    <?= !empty($errors) ? '<p class="error-message">' . implode('<br>', $errors) . '</p>' : '' ?>
    <form action="<?= Utils::getURL('user', 'edit') ?>" method="post">
        <label for="name">Name</label><br>
        <input type="text" name="name" id="name" value="<?= Utils::escapeData($user['name']) ?>"><br>
        <label for="surname">Surname</label><br>
        <input type="text" name="surname" id="surname" value="<?= Utils::escapeData($user['surname']) ?>"><br>
        <label for="email">Email</label><br>
        <input type="email" name="email" id="email" value="<?= Utils::escapeData($user['email']) ?>"><br>
        <fieldset>
            <label for="current-password">Current Password</label><br>
            <input type="password" name="current-password" id="current-password"><br>
            <label for="new-password">New Password</label><br>
            <input type="password" name="new-password" id="new-password"><br>
            <label for="confirm-password">Confirm New Password</label><br>
            <input type="password" name="confirm-password" id="confirm-password"><br>
        </fieldset>
        <input type="submit" value="Edit">
    </form>
</div>
