<?php
if (!empty($error)) {
    echo "<p>$error</p>";
}
?>
<form action="/user/login" method="post">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email">
    <label for="password">Password:</label>
    <input type="password" id="password" name="password">
    <input type="submit" value="Login">
</form>