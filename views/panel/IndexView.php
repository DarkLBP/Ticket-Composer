<?php
/**
 * @var string $ticketCount
 * @var string $openTickets
 * @var string $closedTickets
 * @var array $loggedUser
 * @var array $recentPost
 * @var int $totalTickets
 * @var int $totalPosts
 */
?>

<h2>Welcome Back</h2>
<p>Use the navigation menu links above to get started</p>
<div class="jsHidden">
    <h2>Statistics</h2>
    <p>Total tickets you opened: <?= $totalTickets ?></p>
    <p>Total posts you created: <?= $totalPosts ?></p>
    <?php
    if ($totalPosts !== 0) {
        echo "<p>Your latest post was created on: <time>$recentPost[created]</time></p>";
    }
    ?>
    <h4 class="center" id="slideshow-title"></h4>
    <div class="jsHidden" id="chart-slideshow"></div>
</div>