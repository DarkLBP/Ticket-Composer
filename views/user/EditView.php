<?php
use Core\Utils;

/**
 * @var array $user
 * @var array $loggedUser
 */
?>
<div class="centered-form">
    <h2>Edit User</h2>
    <?= !empty($errors) ? '<p class="error-message">' . implode('<br>', $errors) . '</p>' : '' ?>
    <form action="<?= Utils::getURL('user', 'edit', [$user['id']]) ?>" method="post">
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
        <?php
        if ($loggedUser['op'] == 1) {
            if (!empty($departments)) {
                echo "<label for='departments'>Departments</label><br>";
                echo "<select name='departments[]' id='departments' multiple>";
                foreach ($departments as $department) {
                    echo "<option value='$department[id]'";
                    if (in_array($department["id"], $user['departments'])) {
                        echo " selected";
                    }
                    echo ">$department[name]</option>";
                }
                echo "</select>";
            }
            if ($user['id'] != $loggedUser['id']) {
                echo "<label for='op'>";
                echo "<input type='checkbox' name='op' id='op' value='op' " . ($user['op'] == 1 ? 'checked' : '') . ">Op";
                echo "</label>";
            }
        }
        ?>
        <div class="row">
            <input type="submit" value="Edit User">
            <?php
            if ($user['id'] != $loggedUser['id']) {
                echo '<a href="' . Utils::getURL('user', 'delete', [$user['id']]) . '" class="button danger">Remove User</a>';
            }
            ?>
        </div>
    </form>
</div>
