<?php
use Core\Utils;
/**
 * @var array $post
 */
?>
<form action="<?= Utils::getURL('tickets', 'deletePost', [$post['id']]) ?>" method="post">
    <p>Are you sure you want to delete this post?</p>
    <input type="submit" value="Delete"><br>
    <a href="<?= Utils::getURL('tickets', 'view', [$post['ticketId']]) ?>">Go Back</a>
</form>
