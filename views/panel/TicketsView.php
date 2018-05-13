<?php
use Core\Utils;
/**
 * @var array $tickets
 * @var string $sortBy
 * @var string $sortOrder
 * @var string $searchTerm
 */
?>
<h2>Tickets</h2>
<?php
$queryString = '?';
if (!empty($searchTerm)) {
    $queryString .= "search=$searchTerm";
    echo "<h4>Filtering results by: $searchTerm</h4>";
}
?>
<div class="row">
    <a href="<?= Utils::getURL('ticket', 'create') ?>" class="button small">Create New Ticket</a>
    <div id="search-box">
        <form action="<?= Utils::getURL('panel', 'tickets') ?>" method="get">
            <input type="search" name="search" id="search" placeholder="Search...">
            <input type="submit" class="button small" value="&#128269;">
        </form>
    </div>
</div>
<?php
if (empty($tickets)) {
    echo "<p>There are no tickets</p>";
} else {
?>

<table class="tickets">
    <thead>
    <tr>
        <th>
            <?php
            $query = strlen($queryString) > 1 ? "$queryString&" : $queryString;
            if ($sortBy === "id") {
                if ($sortOrder === 'asc') {
                    $query .= "sort=id&order=desc";
                }
            } else {
                $query .= "sort=id&order=asc";
            }
            echo "<a href='" . Utils::getURL('panel', 'tickets') . $query ."'>Id</a>";
            ?>
        </th>
        <th>
            <?php
            $query = strlen($queryString) > 1 ? "$queryString&" : $queryString;
            if ($sortBy === "title") {
                if ($sortOrder === 'asc') {
                    $query .= "sort=title&order=desc";
                }
            } else {
                $query .= "sort=title&order=asc";
            }
            echo "<a href='" . Utils::getURL('panel', 'tickets') . $query ."'>Title</a>";
            ?>
        </th>
        <th id="f-departmentName">
            <?php
            $query = strlen($queryString) > 1 ? "$queryString&" : $queryString;
            if ($sortBy === "departmentName") {
                if ($sortOrder === 'asc') {
                    $query .= "sort=departmentName&order=desc";
                }
            } else {
                $query .= "sort=departmentName&order=asc";
            }
            echo "<a href='" . Utils::getURL('panel', 'tickets') . $query ."'>Department</a>";
            ?>
        </th>
        <th>
            <?php
            $query = strlen($queryString) > 1 ? "$queryString&" : $queryString;
            if ($sortBy === "lastReply") {
                if ($sortOrder === 'asc') {
                    $query .= "sort=lastReply&order=desc";
                }
            } else {
                $query .= "sort=lastReply&order=asc";
            }
            echo "<a href='" . Utils::getURL('panel', 'tickets') . $query ."'>Last Reply</a>";
            ?>
        </th>
        <th>
            <?php
            $query = strlen($queryString) > 1 ? "$queryString&" : $queryString;
            if ($sortBy === "totalPosts") {
                if ($sortOrder === 'asc') {
                    $query .= "sort=totalPosts&order=desc";
                }
            } else {
                $query .= "sort=totalPosts&order=asc";
            }
            echo "<a href='" . Utils::getURL('panel', 'tickets') . $query ."'>Replies</a>";
            ?>
        </th>
        <th>
            <?php
            $query = strlen($queryString) > 1 ? "$queryString&" : $queryString;
            if ($sortBy === "open") {
                if ($sortOrder === 'asc') {
                    $query .= "sort=open&order=desc";
                }
            } else {
                $query .= "sort=open&order=asc";
            }
            echo "<a href='" . Utils::getURL('panel', 'tickets') . $query ."'>Status</a>";
            ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <?php
        foreach ($tickets as $ticket) {
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