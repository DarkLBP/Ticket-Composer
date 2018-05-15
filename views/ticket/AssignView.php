<?php

use Core\Utils;

/**
 * @var array $users
 * @var string $error
 * @var string $ticketId
 */
?>
<div class="centered-form">
    <h2>Assign Ticket To</h2>
    <?= !empty($error) ? '<p class="error-message">' . $error . '</p>' : '' ?>
    <form action="<?= Utils::getURL('ticket', 'assign', [$ticketId]) ?>" method="post">
        <label for="user">User</label>
        <select id="user" name="user">
            <option value="">Select a user</option>
            <?php
            foreach ($users as $user) {
                echo "<option value='$user[id]'>$user[name] $user[surname]</option>";
            }
            ?>
        </select><br>
        <input type="submit" value="Assign User">
    </form>
</div>
