<?php
use Core\Utils;
/**
 * @var array $departments
 * @var string $sortBy
 * @var string $sortOrder
 * @var string $searchTerm
 * @var int $morePages
 * @var int $page
 */
?>
<h2>Departments</h2>
<?php
$queryString = '?';
if (!empty($searchTerm)) {
    $queryString .= "search=$searchTerm";
    echo "<h4>Filtering results by: $searchTerm</h4>";
}
if ($page != 1) {
    if (empty($searchTerm)) {
        $queryString .= "page=$page";
    } else {
        $queryString .= "&page=$page";
    }
}
?>
<div class="row between">
    <a href="<?= Utils::getURL('department', 'create') ?>" class="button small">Add New Department</a>
    <div id="search-box">
        <form action="<?= Utils::getURL('panel', 'departments') ?>" method="get">
            <input type="search" name="search" id="search" placeholder="Search...">
            <input type="submit" class="button small" value="&#128269;">
        </form>
    </div>
</div>
<?php
if (empty($departments)) {
    echo "<p>There are no departments</p>";
} else {
?>
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
        <th class="hide-medium">
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
        echo "<td class=\"hide-medium\"><time>$department[created]</time></td>";
        echo "</tr>";
    }
    ?>
    </tbody>
</table>
<?php
    $queryString = '?';
    if (!empty($searchTerm)) {
        $queryString .= "search=$searchTerm";
    }
    if (!empty($sortBy) && !empty($sortOrder)) {
        if (empty($searchTerm)) {
            $queryString .= "sort=$sortBy&order=$sortOrder";
        } else {
            $queryString .= "&sort=$sortBy&order=$sortOrder";
        }
    }
    if ($page != 1 || $morePages) {
        echo "<div class='row'>";
    }
    if ($page != 1) {
        if (empty($searchTerm) && empty($sortBy) && empty($sortOrder)) {
            $append = "page=" . ($page - 1);
        } else {
            $append = "&page=" . ($page - 1);
        }
        echo "<a href='" . Utils::getURL('panel', 'departments') . $queryString . $append . "' class='button small'>Previous Page</a>";
    }
    if ($morePages) {
        if (empty($searchTerm) && empty($sortBy) && empty($sortOrder)) {
            $append = "page=" . ($page + 1);
        } else {
            $append = "&page=" . ($page + 1);
        }
        echo "<a href='" . Utils::getURL('panel', 'departments') . $queryString . $append . "' class='button small'>Next Page</a>";
    }
    if ($page != 1 || $morePages) {
        echo "</div>";
    }
}
?>