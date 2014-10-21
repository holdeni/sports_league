<?php
// $Id: leag_registration.php 232 2012-04-13 19:33:56Z Henry $
// Last Change: $Date: 2012-04-13 15:33:56 -0400 (Fri, 13 Apr 2012) $

/* ... data declarations */
$todaysDate = date( "Y-m-d" );
?>

<h1>League Registration</h1>

<p>
From this page you can register either a team, yourself as a spare player looking to find a team to play on or even a group of
players looking to play together. Before
you register for either, please ensure you have reviewed the league information and understand the structure of the league.
If you register as a spare player, you can return here later to remove yourself if you find a team to play on, either in this
league or another.
</p>

<?php
/* ... if we have a status message to display, then show it here */
if (array_key_exists( 'statusMsg', $_SESSION )) {
?>
<p class="success">Status: <?= $_SESSION['statusMsg'] ?></p>

<?php
   unset( $_SESSION['statusMsg'] );
 }
?>

<h2>Registration Options</h2>

<?php
if (!$this->Model_Account->amILoggedIn()) {
?>
<p>
Before you register you MUST create an account on this website and be logged in. If you haven't done so, use the <em>register</em>
link located in the login panel area to the left. After logging in, the links below will be available for use.
</p>

<ul>
<?php
if (strtotime( $this->config->item( 'registration_team_begin' ) ) <= strtotime( $todaysDate )  &&  strtotime( $todaysDate ) <= strtotime( $this->config->item( 'registration_team_end' ) ) ) {
?>
   <li>Register team for season</li>
   <br />
<?php
}
if (strtotime( $this->config->item( 'registration_player_begin' ) ) <= strtotime( $todaysDate )  &&  strtotime( $todaysDate ) <= strtotime( $this->config->item( 'registration_player_end' ) ) ) {
?>
   <li>Register as a spare player / group (or update an existing registration)</li>
   <br />
<?php
}
?>
	<li>View list of spares</li>
</ul>

<?php
 }
else {
?>

<ul>
<?php
if (strtotime( $this->config->item( 'registration_team_begin' ) ) <= strtotime( $todaysDate )  &&  strtotime( $todaysDate ) <= strtotime( $this->config->item( 'registration_team_end' ) ) ) {
?>
   <li><?= anchor( "league/leag_register/registerTeam", "Register team for season" ) ?></li>
   <br />
<?php
}
if (strtotime( $this->config->item( 'registration_player_begin' ) ) <= strtotime( $todaysDate )  &&  strtotime( $todaysDate ) <= strtotime( $this->config->item( 'registration_player_end' ) ) ) {
?>
   <li><?= anchor( "league/leag_register/registerSpare", "Register as a spare player / group (or update an existing registration)" ) ?> </li>
   <br />

<?php
}
?>
	<li><?= anchor( "league/leag_register/listSpares", "View list of spares" ) ?></li>
</ul>

<?php
}
