<?php
use Core\Utils;
/**
 * @var array $ticket
 * @var array $ticketPosts
 * @var string $error
 * @var array $loggedUser
 */
?>
<h2><?= $ticket['title'] ?></h2>
<h5>Department: <?= $ticket['departmentName'] ?></h5>
<h5>Status:
    <?php
        if ($ticket['open'] == 1) {
            echo "<span class='open'>Open</span> ";
            echo '<a class="button" href="' . Utils::getURL('ticket', 'close', [$ticket['id']]) . '">Close</a>';
        } else {
            echo "<span class='closed'>Closed</span> ";
            echo '<a class="button" href="' . Utils::getURL('ticket', 'open', [$ticket['id']]) . '">Reopen</a>';
        }
    ?>
</h5>
<section id="ticket-posts">
    <?php
    foreach ($ticketPosts as $post) {
        echo "<article><header class='ticket-header'>";
        echo "<h4>$post[createdName] $post[createdSurname]</h4>";
        echo "<h5>On $post[created]</h5>";
        echo "</header>";
        echo "<p class='ticket-content'>" . str_replace(["\n", "\r\n"], '<br>', htmlspecialchars($post['content'])) . "</p>";
        echo "<footer class='ticket-footer'>";
        if (!empty($post['attachments'])) {
            echo '<h5>Attachments:</h5>';
            foreach ($post['attachments'] as $attachment) {
                echo "<a href='" . Utils::getURL('attachment', 'download', [$attachment['id']]) . "'>$attachment[fileName]</a><br>";
            }
        }
        if ($post['userId'] == $loggedUser['id']) {
            echo "<a href='" . Utils::getURL('post', 'edit', [$post["id"]]) . "' class='button small'>Edit</a>";
        }
        if ($loggedUser['op'] == 1) {
            echo " <a href='" . Utils::getURL('post', 'delete', [$post["id"]]) . "' class='button small danger'>Delete</a>";
        }
        echo "</footer>";
        echo "</article>";
    }
    ?>
</section>
<div class="centered-form">
<?php if ($ticket['open'] == 1):?>
        <h3>Post New Message</h3>
        <?= empty($error) ? '' : "<p class='error-message'>$error</p>"; ?>
        <form action="<?= Utils::getURL('post', 'create', [$ticket["id"]]) ?>" method="post" enctype="multipart/form-data">
            <label for="message">Message:</label><br>
            <textarea id="message" name="message"></textarea><br>
            <label for="attachment">Attachment:</label><br>
            <input type="file" name="attachment" id="attachment"><br>
            <label for="close">
                <input type="checkbox" name="close" id="close" value="close"> Close Ticket
            </label>
            <input type="submit" value="Post Message">
        </form>

<?php else: ?>
    <p class="error-message center">This ticket is already closed. No further posts are allowed.</p>
<?php endif ?>
</div>
