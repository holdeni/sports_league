<?php
// $Id: sched_home.php 197 2011-08-07 15:00:30Z Henry $
// Last Change: $Date: 2011-08-07 11:00:30 -0400 (Sun, 07 Aug 2011) $

/* ... data declarations */
$todaysDate = date( "Y-m-d" );
?>

<div>
<h1>League Schedules - Regular Season</h1>

<p>
From this page you can view the latest regular season league schedule in a variety of ways.
</p>

<ul>
   <li>
      <?= anchor( "schedule/sched_mainpage/showMaster", "View master schedule" ) ?>
   </li>

   <br />
   <li>
      View specific team's schedule &nbsp;

<?php
echo form_open( "schedule/sched_mainpage/showTeam" );
$selectorAttributes = "";
$i = 0;
foreach ($data[$formFields[$i]['fieldName']] as $attribute => $value) {
   $selectorAttributes .= $attribute."='".$value."' ";
 }
?>
   <?= form_dropdown( $formFields[$i]['fieldName'], $options[$formFields[$i]['fieldName']], $formFields[$i]['default'] != "" ? $formFields[$i]['default'] : "", $selectorAttributes ) ?>
   &nbsp;

<?php
echo form_submit( "viewTeam", "View Team Schedule" );
echo form_close();
?>
   </li>

   <br />
   <li>
      View specific division schedule &nbsp;

<?php
echo form_open( "schedule/sched_mainpage/showDivision" );
$selectorAttributes = "";
$i = 0;
foreach ($data2[$formFields2[$i]['fieldName']] as $attribute => $value) {
   $selectorAttributes .= $attribute."='".$value."' ";
 }
?>
   <?= form_dropdown( $formFields2[$i]['fieldName'], $options2[$formFields2[$i]['fieldName']], $formFields2[$i]['default'] != "" ? $formFields2[$i]['default'] : "", $selectorAttributes ) ?>
   &nbsp;

<?php
echo form_submit( "viewDiv", "View Div Schedule" );
echo form_close();
?>
   </li>

   <br />
   <li>
      <?= anchor( "schedule/sched_mainpage/showOpenSlots", "View open time slots" ) ?>
   </li>

</ul>
</div>

<br />

<div>
   <h1>League Schedules - Playoff Tournament</h1>
   
   <ul>
      <li><?= anchor( "schedule/sched_tournament/showGames", "View all tournament games" ) ?></li>
      <br />
      <li><?= anchor( "schedule/sched_tournament/showGames/A", "Show Division A tournament" ) ?></li>
      <li><?= anchor( "schedule/sched_tournament/showGames/B", "Show Division B tournament" ) ?></li>
      <li><?= anchor( "schedule/sched_tournament/showGames/C", "Show Division C tournament" ) ?></li>
      <br />
<!--      <li><?= anchor( "schedule/sched_tournament/showOpenSlots", "View open time slots" ) ?></li> -->
   </ul>
</div>