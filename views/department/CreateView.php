<?php
use Core\Utils;
if (isset($error)) {
    echo "<p>$error</p>";
}
?>
<form action="<?= Utils::getURL('department', 'create') ?>" method="post">
    <label for="name">Name:</label><br>
    <input type="text" name="name" id="name"><br>
    <input type="submit" value="Create">
</form>