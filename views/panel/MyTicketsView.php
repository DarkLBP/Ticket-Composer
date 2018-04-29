<?php
use Core\Utils;
/**
 * @var array $tickets
 */
?>
<h2>My Tickets</h2>
<table>
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
        echo "<tr>";
        echo "<td><a href='" . Utils::getURL('tickets', 'view', [$ticket['id']]) . "'>$ticket[id]</a></td>";
        echo "<td>$ticket[title]</td>";
        echo "<td>$ticket[departmentName]</td>";
        if (!empty($ticket["asignedTo"])) {
            echo "<td>$ticket[asignedTo]</td>";
        } else {
            echo "<td>Unassigned</td>";
        }
        echo "<td>$ticket[created]</td>";
        if ($ticket["open"] == 1) {
            echo "<td>Open</td>";
        } else {
            echo "<td>Closed</td>";
        }
        echo "<tr>";
    }
    ?>
    </tbody>
</table>