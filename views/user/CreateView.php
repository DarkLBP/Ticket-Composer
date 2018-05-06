<?php
use Core\Utils;

/**
 * @var array $departments
 */
?>
<div class="centered-form">
    <h2>Create User</h2>
    <?= !empty($errors) ? '<p class="error-message">' . implode('<br>', $errors) . '</p>' : '' ?>
    <form method="post" action="<?= Utils::getURL("user", "create") ?>">
        <label for="name">Name</label><br>
        <input type="text" id="name" name="name"><br>
        <label for="surname">Surname</label><br>
        <input type="text" id="surname" name="surname"><br>
        <label for="email">Email</label><br>
        <input type="email" id="email" name="email"><br>
        <label for="password">Password</label><br>
        <input type="password" id="password" name="password"><br>
        <label for="confirm">Confirm Password</label><br>
        <input type="password" id="confirm" name="confirm"><br>
        <label for='departments'>Departments</label><br>
        <select name='departments[]' id='departments' multiple>
            <?php
            foreach ($departments as $department) {
                echo "<option value='$department[id]'>$department[name]</option>";
            }
            ?>
        </select>
        <label for='op'>
            <input type='checkbox' name='op' id='op' value='op'>Op
        </label>
        <input type="submit" value="Create User">
    </form>
</div>