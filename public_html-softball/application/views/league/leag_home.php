<?php
// $Id: leag_home.php 195 2011-08-07 14:44:36Z Henry $
// Last Change: $Date: 2011-08-07 10:44:36 -0400 (Sun, 07 Aug 2011) $

/* ... data declarations */
$todaysDate = date( "Y-m-d" );

/* ... see what role the current user has */
$userRole = $this->Model_Account->getAccountRole( $_SESSION['UserId'] );

/* ... get the number of rainout games requiring rescheduling (for display beside the appropriate option) */
$nrRainouts = $this->Model_Schedule->getNumberOfRainouts();
?>

<h1>League Administration</h1>

<p>
From this page you can perform various league administrative functions. You are only able to perform those
actions that you have been authorized for by the webmaster.
</p>

<?php
if ($this->Model_Account->hasAuthority( "COMMISH" )) {
?>
<h2>Team Operations</h2>
<ul>
   <li><?= anchor( "league/leag_mainpage/registerTeam", "Register new team" ) ?></li>
   <br />
   <li><?= anchor( "league/leag_mainpage/updateTeam", "Update team details" ) ?> [for existing teams -- including changing team contacts]</li>
</ul>

<?php
 }


if ($this->Model_Account->hasAuthority( "COMMISH" )) {
?>
<h2>Schedule Operations</h2>
<ul>

<?php
   if ($this->Model_Account->hasAuthority( "ADMIN" )) {
?>
   <li><?= anchor( "schedule/sched_load/index", "Load schedule" ) ?> [from CSV data file]</li>
   <br />
   <li><?= anchor( "schedule/sched_tournament_load/index", "Load tournament" ) ?> [from CSV data file]</li>
   <br />
   
<?php
    }
   if ($this->Model_Account->hasAuthority( "COMMISH" )) {
?>
   <li><?= anchor( "schedule/sched_rainouts", "Reschedule rainouts" ) ?> (<?= $nrRainouts ?> rainouts to reschedule)</li>
   <br />
   <li><?= anchor( "schedule/sched_standings/seedPlayoffs", "Update playoff schedule" ) ?> with teams as seeded by current standings</li>

<?php
    }
?>

</ul>

<?php
 }
