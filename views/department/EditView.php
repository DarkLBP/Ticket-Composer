<?php
use Core\Utils;
/**
 * @var array $department
 */
if (isset($error)) {
    echo "<p>$error</p>";
}
?>
<form action="<?= Utils::getURL('department', 'edit', [$department['id']]) ?>" method="post">
    <label for="department">New Name</label><br>
    <input type="text" name="department" id="department" value="<?= $department['name'] ?>"><br>
    <input type="submit" value="Edit">
</form>
