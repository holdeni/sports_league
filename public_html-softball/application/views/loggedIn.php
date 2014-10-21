<?php
// $Id: loggedIn.php 202 2011-08-23 17:02:14Z Henry $
// Last Change: $Date: 2011-08-23 13:02:14 -0400 (Tue, 23 Aug 2011) $

/* ... if we are a captain, get our team name */
$teamName = "";
if (array_key_exists( "TeamID", $_SESSION )) {
   $teamName = $this->Model_Team->getTeamName( $_SESSION['TeamID'] );
 }

/* ... if we just logged in, display a welcoming message */
if (array_key_exists( "freshLogin", $_SESSION )) {
?>
<p class="success">Login successful</p>

<?php
   unset( $_SESSION['freshLogin'] );
 }
?>

<p class="info">Hello, <?= $_SESSION['FirstName']." ".$_SESSION['LastName'].($teamName != "" ? " (".htmlspecialchars( $teamName ).")" : "") ?></p>

<ul>
   <li><?= anchor( "profile/index", "my account" ) ?></li>
   <li><?= anchor( "mainpage/logout", "logout" ) ?></li>

<?php
/* ... add any Captain related actions */
if ($this->Model_Account->hasAuthority( "CAPTAIN" )) {
?>
   <h4>Team Actions</h4>
   <li><?= anchor( "schedule/sched_scores/index", "report game result" ) ?></li>
   <li><?= anchor( "schedule/sched_tournament/report", "report playoff game" ) ?></li>
   <li><?= anchor( "league/leag_updateTeam", "update team contacts" ) ?></li>

<?php
 }

/* ... add any Commish related actions */
if ($this->Model_Account->hasAuthority( "COMMISH" )) {
?>
   <h4>League Actions</h4>
   <li><?= anchor( "league/leag_mainpage/index", "admin league" ) ?></li>

<?php
 }

/* ... add any Admin related actions */
if ($this->Model_Account->hasAuthority( "ADMIN" )) {
?>
   <h4>Site Actions</h4>
   <li><?= anchor( "webAdmin/admin_website/index", "admin site" ) ?></li>

<?php
 }
?>
</ul>