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
<h5>Status: <?= $ticket['open'] == 1 ? "<span class='open'>Open</span>" : "<span class='closed'>Closed</span>" ?></h5>
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
            echo " <a href='" . Utils::getURL('post', 'delete', [$post["id"]]) . "' class='button small'>Delete</a>";
        }
        echo "</footer>";
        echo "</article>";
    }
    ?>
</section>
<?php if ($ticket['open'] == 1 || $loggedUser['op'] == 1):?>
    <div class="centered-form">
        <h3>Post New Message</h3>
        <?= empty($error) ? '' : "<p class='error-message'>$error</p>"; ?>
        <form action="<?= Utils::getURL('post', 'create', [$ticket["id"]]) ?>" method="post" enctype="multipart/form-data">
            <label for="message">Message:</label><br>
            <textarea id="message" name="message"></textarea><br>
            <label for="attachment">Attachment:</label><br>
            <input type="file" name="attachment" id="attachment"><br>
            <input type="submit" value="Post Message">
        </form>
    </div>
<?php endif ?>
