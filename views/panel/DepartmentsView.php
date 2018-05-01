<?php
use Core\Utils;
/**
 * @var array $departments
 */
?>
<h2>Departments</h2>
<table>
    <thead>
    <tr>
        <th>Id</th>
        <th>Name</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($departments as $department) {
        echo "<tr>";
        echo "<td>$department[id]</td>";
        echo "<td>$department[name]</td>";
        echo "<td>";
        echo "<a href='" . Utils::getURL('department', 'edit', [$department['id']]) . "'>Edit</a> ";
        echo "<a href='" . Utils::getURL('department', 'delete', [$department['id']]) . "'>Delete</a>";
        echo "</tr>";
    }
    ?>
    </tbody>
</table>
<a href="<?= Utils::getURL('department', 'create') ?>">Add New</a>