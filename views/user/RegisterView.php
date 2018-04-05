<?php
if (!empty($error)) {
    echo "<p>$error</p>";
}
?>
<form method="post" action="/user/register">
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
    <input type="submit" value="Register">
</form>