<?php
use Core\Utils;
/**
 * @var array $users
 */
?>
<h2>Users</h2>
<table>
    <thead>
    <tr>
        <th>Id</th>
        <th>Name</th>
        <th>Surname</th>
        <th>Email</th>
        <th>Created</th>
        <th>Op</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td><a href='" . Utils::getURL('user', 'edit', [$user['id']]) . "'>$user[id]</a></td>";
        echo "<td>$user[name]</td>";
        echo "<td>$user[surname]</td>";
        echo "<td>$user[email]</td>";
        echo "<td>$user[created]</td>";
        if ($user['op'] == 1) {
            echo '<td>Yes</td>';
        } else {
            echo '<td>No</td>';
        }
        echo "</tr>";
    }
    ?>
    </tbody>
</table>
<a href="<?= Utils::getURL('user', 'create') ?>">Add New</a>