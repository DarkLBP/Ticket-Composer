<?php
use Core\Utils;
?>
<h2>Welcome to <?= SITE_TITLE ?></h2>
<p><a href="<?= Utils::getURL('user', 'register')?>">Create an account now</a> to begin creating tickets</p>
<p>Once you create an account you will receive an email prompting you to validate your account</p>
<p>If you are already registered just <a href="<?= Utils::getURL('user', 'login')?>">log in</a> to enter to your panel</p>
<p>Once in there you can manager your tickets and create new ones</p>
<p>If you are assigned to a department or you are an OverPowered user you will have some extra tools for you to work</p>