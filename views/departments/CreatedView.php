<?php
use Core\Utils;

/**
 * @var string $department
 */
?>
<p>Department '<?= $department ?>' created.</p>
<a href="<?= Utils::getURL('departments', 'create') ?>">Return</a>