<?php
use Core\Utils;
/**
 * @var string $department
 * @var string $name
 */
?>
<div class="centered-form">
    <h2>Delete Department</h2>
    <form action="<?= Utils::getURL('department', 'delete', [$department])?>" method="post">
        <p>Are you sure you want to delete <?= $name ?>?</p>
        <input type="submit" value="Delete">
        <a href="<?= Utils::getURL('panel', 'departments') ?>">Go back</a>
    </form>
</div>
