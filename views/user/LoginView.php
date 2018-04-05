<?php
use Core\Utils\Html;

Html::beginForm("post");
?>
<label for="email">Email:</label>
<input type="email" id="email" name="email">
<label for="password">Password:</label>
<input type="password" id="password" name="password">
<?php
Html::submit("Login");
Html::endForm();