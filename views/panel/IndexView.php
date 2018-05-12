<?php
/**
 * @var string $ticketCount
 * @var string $openTickets
 * @var string $closedTickets
 * @var array $loggedUser
 */
?>

<h2>Welcome Back</h2>
<p>Use the navigation menu links above to get started</p>
<?php if ($loggedUser['op'] == 1) : ?>
    <h2>Statistics</h2>

<?php endif; ?>