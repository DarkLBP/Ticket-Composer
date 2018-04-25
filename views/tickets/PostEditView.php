<?php
/**
 * @var array $post
 */
use Core\Utils;
if (!empty($error)) {
    echo "<p>$error</p>";
}
?>
<form action="<?= Utils::getURL('tickets', 'editPost', [$post['id']]) ?>" method="post">
    <label for="message">Message:</label><br>
    <textarea id="message" name="message"><?= htmlspecialchars($post['content']) ?></textarea><br>
    <input type="submit" value="Edit">
</form>
