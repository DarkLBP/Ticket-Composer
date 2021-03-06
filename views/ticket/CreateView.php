<?php

use Core\Utils;

/**
 * @var array $departments
 * @var array $errors
 */
?>
<div class="centered-form">
    <h2>Create Ticket</h2>
    <?= !empty($errors) ? '<p>' . implode('<br>', $errors) . '</p>' : '' ?>
    <form action="<?= Utils::getURL('ticket', 'create') ?>" method="post" enctype="multipart/form-data">
        <label for="title">Title</label><br>
        <input type="text" name="title" id="title" required><br>
        <label for="department">Department</label><br>
        <select id="department" name="department" required>
            <option value="">Select a department</option>
            <?php
            foreach ($departments as $department) {
                echo "<option value='$department[id]'>$department[name]</option>";
            }
            ?>
        </select><br>
        <label for="content">Content</label><br>
        <textarea name="content" id="content" required></textarea><br>
        <label for="attachment">Attachments</label><br>
        <div id="attachments">
            <input type="file" name="attachment[]" id="attachment">
        </div>
        <a id="addAttachment" class="jsHidden button small">Add Attachment</a>
        <a id="removeAttachment" class="jsHidden button small">Remove Attachment</a>
        <input type="submit" value="Create Ticket">
    </form>
</div>
