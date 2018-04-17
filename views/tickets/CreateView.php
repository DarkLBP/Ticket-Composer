<?php
use Core\Utils;

/**
 * @var array $departments
 */
?>
<form action="<?= Utils::getURL('tickets', 'create')?>" method="post">
    <label for="title">Title:</label><br>
    <input type="text" name="title" id="title"><br>
    <label for="department">Department:</label><br>
    <select id="department" name="department">
        <option>Select a department</option>
        <?php
        foreach ($departments as $department) {
            echo "<option value='$department[id]'>$department[name]</option>";
        }
        ?>
    </select><br>
    <label for="content">Content:</label><br>
    <textarea name="content" id="content"></textarea><br>
    <input type="submit" value="Create">
</form>
