<?php
use Core\Utils;
/**
 * @var array $department
 */
?>
<div class="centered-form">
    <h2>Edit Department</h2>
    <?= !empty($error) ? "<p class='error-message'>$error</p>" : '' ?>
    <form action="<?= Utils::getURL('department', 'edit', [$department['id']]) ?>" method="post">
        <label for="department">New Name</label><br>
        <input type="text" name="department" id="department" value="<?= $department['name'] ?>" required><br>
        <div class="row">
            <input type="submit" value="Edit Department">
            <?='<a href="' . Utils::getURL('department', 'delete', [$department['id']]) . '" class="button danger">Remove Department</a>' ?>
        </div>
    </form>
</div>
