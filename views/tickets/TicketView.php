<?php
use Core\Utils;
/**
 * @var array $ticket
 * @var array $ticketPosts
 * @var string $error
 */
?>
<h2>Ticket #<?= $ticket['id'] ?></h2>
<h3><?= $ticket['title'] ?></h3>
<h5>Created by <?= $ticket['createdName'] . ' ' . $ticket['createdSurname'] ?></h5>
<hr>
<?php
foreach ($ticketPosts as $post) {
    echo "<article>";
    echo "<header>";
    echo "<p>Created by <a href='" . Utils::getURL('users', 'view', [$post['createdId']]) . "'>$post[createdName] $post[createdSurname]</a></p>";
    echo "<small>Created on $post[created]</small>";
    echo "</header>";
    echo "<p>" . str_replace(["\n", "\r\n"], '<br>', htmlspecialchars($post['content'])) . "</p>";
    echo "</article><hr>";
}
echo empty($error) ? '' : "<p>$error</p>";
?>
<form action="<?= Utils::getURL('tickets', 'post', [$ticket["id"]]) ?>" method="post">
    <label for="message">Message:</label><br>
    <textarea id="message" name="message"></textarea><br>
    <input type="submit" value="Post">
</form>
