<?php
use Core\Utils;
/**
 * @var array $user
 */
?>
<div class="centered-form">
    <form action="<?= Utils::getURL('user', 'delete', [$user['id']]) ?>" method="post">
        <h2>Delete User</h2>
        <p>Are you sure you want to delete '<?= $user["name"] . ' ' . $user['surname'] ?>'?</p>
        <div class="row stretch">
            <input type="submit" class="danger" value="Delete User">
            <a href="<?= Utils::getURL('user', 'edit', [$user["id"]]) ?>" class="button">Go Back</a>
        </div>
    </form>
</div>
