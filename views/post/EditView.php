<?php
/**
 * @var array $post
 */
use Core\Utils;
?>
<div class="centered-form">
    <h2>Edit Post</h2>
    <?= !empty($error) ? "<p>$error</p>" : '' ?>
    <form action="<?= Utils::getURL('post', 'edit', [$post['id']]) ?>" method="post">
        <label for="message">Message:</label><br>
        <textarea id="message" name="message" required><?= Utils::escapeData($post['content']) ?></textarea><br>
        <input type="submit" value="Edit Post">
    </form>
</div>
