<?php
use Core\Utils;
/**
 * @var string $name
 * @var string $department
 */
if (isset($error)) {
    echo "<p>$error</p>";
}
?>
<form action="<?= Utils::getURL('departments', 'edit', [$department]) ?>" method="post">
    <label for="department">New Name</label><br>
    <input type="text" name="department" id="department" placeholder="<?= $name ?>"><br>
    <input type="submit" value="Edit">
</form>
