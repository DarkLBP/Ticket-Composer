<?php

use Core\Utils;

?>
<div class="centered-form">
    <h2>New Department</h2>
    <?= !empty($error) ? "<p class='error-message'>$error</p>" : '' ?>
    <form action="<?= Utils::getURL('department', 'create') ?>" method="post">
        <label for="name">Name</label><br>
        <input type="text" name="name" id="name" required><br>
        <input type="submit" value="Create Department">
    </form>
</div>