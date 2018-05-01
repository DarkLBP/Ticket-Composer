<?php
use Core\Utils;
/**
 * @var array $tickets
 */
?>
<h2>My Tickets</h2>
<table class="tickets">
    <thead>
    <tr>
        <th>Ticket Id</th>
        <th>Title</th>
        <th>Department</th>
        <th>Asigned To</th>
        <th>Created On</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($tickets as $ticket) {
        if ($ticket["open"] == 1) {
            echo "<tr class='open'>";
        } else {
            echo "<tr class='closed'>";
        }
        echo "<td><a href='" . Utils::getURL('ticket', 'view', [$ticket['id']]) . "'>$ticket[id]</a></td>";
        echo "<td>$ticket[title]</td>";
        echo "<td>$ticket[departmentName]</td>";
        if (!empty($ticket["asignedTo"])) {
            echo "<td>$ticket[asignedTo]</td>";
        } else {
            echo "<td>Unassigned</td>";
        }
        echo "<td>$ticket[created]</td>";
        if ($ticket["open"] == 1) {
            echo "<td class='open'>Open</td>";
        } else {
            echo "<td class='closed'>Closed</td>";
        }
        echo "</tr>";
    }
    ?>
    </tbody>
</table>