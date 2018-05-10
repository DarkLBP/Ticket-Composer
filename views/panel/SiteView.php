<?php
use Core\Utils;
?>
<h2>Site Actions</h2>
<a href="<?= Utils::getURL('export', 'posts') ?>" class="button">Export Posts</a><br>
<a href="<?= Utils::getURL('export', 'tickets') ?>" class="button">Export Tickets</a><br>
<a href="<?= Utils::getURL('export', 'users') ?>" class="button">Export Users</a><br>
<a href="<?= Utils::getURL('export', 'attachments') ?>" class="button">Export Attachments</a><br>
<a href="<?= Utils::getURL('export', 'departments') ?>" class="button">Export Departments</a>
