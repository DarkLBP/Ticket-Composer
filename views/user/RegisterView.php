<?php
use Core\Utils\Html;

Html::beginForm("post");
?>
<label for="name">Name:</label>
<input type="text" id="name" name="name">
<label for="surname">Surname:</label>
<input type="text" id="surname" name="surname">
<label for="email">Email:</label>
<input type="email" id="email" name="email">
<label for="password">Password:</label>
<input type="password" id="password" name="password">
<label for="confirm">Confirm Password:</label>
<input type="password" id="confirm" name="confirm">
<?php
Html::submit("Register");
Html::endForm();