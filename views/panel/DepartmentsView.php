<?php
use Core\Utils;
/**
 * @var array $departments
 */
?>
<h2>Departments</h2>
<?php
if (empty($departments)) {
    echo "<p>There are no departments</p>";
} else {
?>
<div id="search-box">
    <form action="<?= Utils::getURL('panel', 'tickets') ?>" method="get">
        <input type="search" name="search" id="search" placeholder="Search...">
        <input type="submit" class="button small" value="&#128269;">
    </form>
</div>
<table class="departments">
    <thead>
    <tr>
        <th>Id</th>
        <th>Name</th>
        <th>Created</th>
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