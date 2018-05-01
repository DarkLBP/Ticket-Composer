<?php
use Core\Utils;
/**
 * @var array $post
 */
?>
<div class="centered-form">
    <form action="<?= Utils::getURL('post', 'delete', [$post['id']]) ?>" method="post">
        <p>Are you sure you want to delete this post?</p>
        <input type="submit" value="Delete"><br>
        <a href="<?= Utils::getURL('ticket', 'view', [$post['ticketId']]) ?>">Go Back</a>
    </form>
</div>
