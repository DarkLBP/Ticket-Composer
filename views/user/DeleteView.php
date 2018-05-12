<?php
use Core\Utils;
/**
 * @var array $user
 */
?>
<div class="centered-form">
    <form action="<?= Utils::getURL('user', 'delete', [$user['id']]) ?>" method="post">
        <p>Are you sure you want to delete '<?= $user["name"] . ' ' . $user['surname'] ?>'?</p>
        <div class="row">
            <input type="submit" class="danger" value="Delete User">
            <a href="<?= Utils::getURL('user', 'edit', [$user["id"]]) ?>" class="button">Go Back</a>
        </div>
    </form>
</div>