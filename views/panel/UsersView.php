<?php
use Core\Utils;
/**
 * @var array $users
 * @var string $sortBy
 * @var string $sortOrder
 * @var string $searchTerm
 */
?>
<h2>Users</h2>
<?php
$queryString = '?';
if (!empty($searchTerm)) {
    $queryString .= "search=$searchTerm";
}
if (!empty($searchTerm)) {
    echo "<h4>Filtering results by: $searchTerm</h4>";
}
if (empty($users)) {
    echo "<p>There are no users</p>";
} else {
?>
<div id="search-box">
    <form action="<?= Utils::getURL('panel', 'users') ?>" method="get">
        <input type="search" name="search" id="search" placeholder="Search...">
        <input type="submit" class="button small" value="&#128269;">
    </form>
</div>
<table class="users">
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
            echo "<a href='" . Utils::getURL('panel', 'users') . $query ."'>Id</a>";
            ?>
        </th>
        <th>
            <?php
            $query = strlen($queryString) > 1 ? "$queryString&" : $queryString;
            if ($sortBy === "name") {
                if ($sortOrder === 'asc') {
                    $query .= "sort=name&order=desc";
                }
            } else {
                $query .= "sort=name&order=asc";
            }
            echo "<a href='" . Utils::getURL('panel', 'users') . $query ."'>Name</a>";
            ?>
        </th>
        <th id="f-departmentName">
            <?php
            $query = strlen($queryString) > 1 ? "$queryString&" : $queryString;
            if ($sortBy === "surname") {
                if ($sortOrder === 'asc') {
                    $query .= "sort=surname&order=desc";
                }
            } else {
                $query .= "sort=surname&order=asc";
            }
            echo "<a href='" . Utils::getURL('panel', 'users') . $query ."'>Surname</a>";
            ?>
        </th>
        <th>
            <?php
            $query = strlen($queryString) > 1 ? "$queryString&" : $queryString;
            if ($sortBy === "email") {
                if ($sortOrder === 'asc') {
                    $query .= "sort=email&order=desc";
                }
            } else {
                $query .= "sort=email&order=asc";
            }
            echo "<a href='" . Utils::getURL('panel', 'users') . $query ."'>Email</a>";
            ?>
        </th>
        <th>
            <?php
            $query = strlen($queryString) > 1 ? "$queryString&" : $queryString;
            if ($sortBy === "created") {
                if ($sortOrder === 'asc') {
                    $query .= "sort=created&order=desc";
                }
            } else {
                $query .= "sort=created&order=asc";
            }
            echo "<a href='" . Utils::getURL('panel', 'users') . $query ."'>Created</a>";
            ?>
        </th>
        <th>
            <?php
            $query = strlen($queryString) > 1 ? "$queryString&" : $queryString;
            if ($sortBy === "op") {
                if ($sortOrder === 'asc') {
                    $query .= "sort=op&order=desc";
                }
            } else {
                $query .= "sort=op&order=asc";
            }
            echo "<a href='" . Utils::getURL('panel', 'users') . $query ."'>Op</a>";
            ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($users as $user) {
        if ($user['op'] == 1) {
            echo "<tr class='red-row' id='u-$user[id]'>";
        } else {
            echo "<tr class='green-row' id='u-$user[id]'>";
        }
        echo "<td><a href='" . Utils::getURL('user', 'edit', [$user['id']]) . "'>$user[id]</a></td>";
        echo "<td>$user[name]</td>";
        echo "<td>$user[surname]</td>";
        echo "<td>$user[email]</td>";
        echo "<td><time>$user[created]</time></td>";
        if ($user['op'] == 1) {
            echo '<td class="red">Yes</td>';
        } else {
            echo '<td class="green">No</td>';
        }
        echo "</tr>";
    }
    ?>
    </tbody>
</table>
<?php } ?>
<a href="<?= Utils::getURL('user', 'create') ?>" class="button">Add New User</a>