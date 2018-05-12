<?php
use Core\Utils;
/**
 * @var array $department
 */
?>
<div class="centered-form">
    <h2>Delete Department</h2>
    <form action="<?= Utils::getURL('department', 'delete', [$department["id"]])?>" method="post">
        <p>Are you sure you want to delete '<?= $department["name"] ?>'?</p>
        <div class="row">
            <input type="submit" value="Delete Department">
            <a href="<?= Utils::getURL('department', 'edit', [$department["id"]]) ?>" class="button">Go Back</a>
        </div>
    </form>
</div>
