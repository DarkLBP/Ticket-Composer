<?php
use Core\Utils;
/**
 * @var array $departments
 * @var string $sortBy
 * @var string $sortOrder
 * @var string $searchTerm
 */
?>
<h2>Departments</h2>
<?php
$queryString = '?';
if (!empty($searchTerm)) {
    $queryString .= "search=$searchTerm";
}
if (!empty($searchTerm)) {
    echo "<h4>Filtering results by: $searchTerm</h4>";
}
if (empty($departments)) {
    echo "<p>There are no departments</p>";
} else {
?>
<div id="search-box">
    <form action="<?= Utils::getURL('panel', 'departments') ?>" method="get">
        <input type="search" name="search" id="search" placeholder="Search...">
        <input type="submit" class="button small" value="&#128269;">
    </form>
</div>
<table class="departments">
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
            echo "<a href='" . Utils::getURL('panel', 'departments') . $query ."'>Id</a>";
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
            echo "<a href='" . Utils::getURL('panel', 'departments') . $query ."'>Name</a>";
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
            echo "<a href='" . Utils::getURL('panel', 'departments') . $query ."'>Created</a>";
            ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($departments as $department) {
        echo "<tr id='d-$department[id]'>";
        echo "<td><a href='" . Utils::getURL('department', 'edit', [$department['id']]) . "'>$department[id]</a></td>";
        echo "<td>$department[name]</td>";
        echo "<td><time>$department[created]</time></td>";
        echo "</tr>";
    }
    ?>
    </tbody>
</table>
<?php } ?>
<a href="<?= Utils::getURL('department', 'create') ?>" class="button">Add New Department</a>