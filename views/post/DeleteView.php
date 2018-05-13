<?php
use Core\Utils;
/**
 * @var array $post
 */
?>
<div class="centered-form">
    <form action="<?= Utils::getURL('post', 'delete', [$post['id']]) ?>" method="post">
        <h2>Delete Post</h2>
        <p>Are you sure you want to delete this post?</p>
        <div class="row stretch">
            <input type="submit" class="danger" value="Delete Post">
            <a href="<?= Utils::getURL('ticket', 'view', [$post['ticketId']]) ?>" class="button">Go Back</a>
        </div>
    </form>
</div>
