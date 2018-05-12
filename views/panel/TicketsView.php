<?php
use Core\Utils;
/**
 * @var array $myTickets
 */
?>
<h2>My Tickets</h2>
<?php
if (empty($myTickets)) {
    echo "<p>You have no tickets</p>";
} else {
?>
<table class="tickets">
    <thead>
    <tr>
        <th>Id</th>
        <th>Title</th>
        <th>Department</th>
        <th>Last Reply</th>
        <th>Replies</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>
    <?php
        foreach ($myTickets as $ticket) {
            if ($ticket["open"] == 1) {
                echo "<tr class='green-row' id='t-$ticket[id]'>";
            } else {
                echo "<tr class='red-row' id='t-$ticket[id]'>";
            }
            echo "<td><a href='" . Utils::getURL('ticket', 'view', [$ticket['id']]) . "'>$ticket[id]</a></td>";
            echo "<td>$ticket[title]</td>";
            echo "<td>$ticket[departmentName]</td>";
            echo "<td><time>$ticket[lastReply]</time></td>";
            echo "<td>$ticket[totalPosts]</td>";
            if ($ticket["open"] == 1) {
                echo "<td class='green'>Open</td>";
            } else {
                echo "<td class='red'>Closed</td>";
            }
            echo "</tr>";
        }
    ?>
    </tbody>
</table>
<?php } ?>
<a href="<?= Utils::getURL('ticket', 'create') ?>" class="button">Create New Ticket</a>