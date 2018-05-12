<?php
use Core\Utils;
/**
 * @var array $users
 */
?>
<h2>Users</h2>
<table class="users">
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
        echo "<tr id='u-$user[id]'>";
        echo "<td><a href='" . Utils::getURL('user', 'edit', [$user['id']]) . "'>$user[id]</a></td>";
        echo "<td>$user[name]</td>";
        echo "<td>$user[surname]</td>";
        echo "<td>$user[email]</td>";
        echo "<td><time>$user[created]</time></td>";
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
<a href="<?= Utils::getURL('user', 'create') ?>" class="button">Add New User</a>