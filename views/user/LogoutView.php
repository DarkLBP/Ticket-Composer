<?php
use Core\Utils;
/**
 * @var array $user
 */
?>
<div class="centered-form">
    <form action="<?= Utils::getURL('user', 'logout', [$user['id']]) ?>" method="post">
        <h2>Logout User</h2>
        <p>Are you sure you want to logout '<?= $user["name"] . ' ' . $user['surname'] ?>'?</p>
        <p>This will close all opened sessions.</p>
        <div class="row stretch">
            <input type="submit" class="danger" value="Logout User">
            <a href="<?= Utils::getURL('user', 'edit', [$user["id"]]) ?>" class="button">Go Back</a>
        </div>
    </form>
</div>
