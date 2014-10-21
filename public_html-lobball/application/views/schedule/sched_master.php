<?php
// $Id: sched_master.php 84 2011-04-13 19:22:44Z Henry $
// Last Change: $Date: 2011-04-13 15:22:44 -0400 (Wed, 13 Apr 2011) $

/* ... data declarations */
$todaysDate = date( "Y-m-d" );

/* ... if we are logged in, offer the user an option to have the schedule emailed to them */
if ($this->Model_Account->amILoggedIn()) {
?>   
   <div id="emailSchedule">

<?php
   echo form_open( "schedule/sched_mainpage/emailSchedule" );
   echo "\n";
   echo form_label( "Use this button to have an HTML copy of the schedule emailed to you.", "submit" );
   echo nbs( 2 );
   echo form_submit( "submit", "Email now" );
   echo nbs( 2 );
   echo "\n";
   echo form_label( "The schedule will appear in the email exactly as it does on the screen.", "submit" );
   echo "\n";
   echo form_close();
   echo "\n";
?>
   </div>

<?php
 }

/* ... start buffering the output (since we may display it on the screen or email it */
ob_start();
?>

<h1><?= $scheduleHeader ?> (As of: <?= $todaysDate ?>)</h1>

<div id="schedule">

   <div id="legend">
   <table border="border">
      <caption>
         Color Legend
      </caption>
      <tr class="played">
         <td>Games has been played and score submitted</td>
      </tr>
      <tr class="rainout">
         <td>Game has been reported as a rainout but not yet rescheduled</td>
      </tr>
   </table>
   </div>

   <div id="scheduleTable">
   <br /> <br />
   <table border="border" width="98%">
      
      <tr>
         <th>Date</th>
         <th>Time</th>
         <th>Diamond</th>
         <th>Division</th>
         <th>Visiting Team</th>
         <th width="32px">&nbsp;</th>
         <th>Home Team</th>
         <th width="32px">&nbsp;</th>
      </tr>
   
<?php
/* ... cycle through the schedule and display all our entries */
   $lastGameDate = "";
   $highlightHomeTeam = false;
   $highlightVisitTeam = false;
   for ($i = 0; $i < count( $schedDetails ); $i++) {
      $gameDate = date( "D, M d Y", strtotime( $schedDetails[$i]['Date'] ) );
      $newGameDate = $gameDate != $lastGameDate ? true : false;
      if ($scheduleFormat['type'] == "team") {
         $highlightHomeTeam = $schedDetails[$i]['HomeTeamID'] == $schedTeamID ? true : false;
         $highlightVisitTeam = $highlightHomeTeam ? false : true;
       }
?>
      <tr class="<?= $newGameDate  &&  $scheduleFormat['type'] != "team" ? "newDay" : "" ?>
                 <?= $schedDetails[$i]['Status'] == "PLAYED" ? "played" : "" ?>
                 <?= $schedDetails[$i]['Status'] == "RAINOUT" ? "rainout" : "" ?>">
         <td class="ML"><?= $newGameDate  ||  $scheduleFormat['type'] == "team" ? $gameDate : "" ?></td>
         <td class="MC"><?= $schedDetails[$i]['Time'] ?></td>
         <td class="MC"><?= ucfirst( strtolower( $schedDetails[$i]['Diamond'] ) ) ?></td>
         <td class="MC"><?= $this->Model_Team->getDivision( $schedDetails[$i]['HomeTeamID'] ) ?></td>
         <td class="<?= $highlightVisitTeam ? "highlightTeam" : "" ?>">
            <?= htmlspecialchars( $this->Model_Team->getTeamName( $schedDetails[$i]['VisitTeamID'] ) ) ?></td>
         <td class="MC"><?= $schedDetails[$i]['VisitScore'] ?></td>
         <td class="<?= $highlightHomeTeam ? "highlightTeam" : "" ?>">
            <?= htmlspecialchars( $this->Model_Team->getTeamName( $schedDetails[$i]['HomeTeamID'] ) ) ?></td>
         <td class="MC"><?= $schedDetails[$i]['HomeScore'] ?></td>
      </tr>
   
<?php
      $lastGameDate = $gameDate;
    }
?>
   </table>
   </div>
   
</div>

<?php
/* ... if we are displaying a team schedule, then we have the 2nd display of the data in calendar format */
if ($scheduleFormat['type'] == "team") {
?>
<div id="schedule-cal" class="schedule-cal">

<p>For games including results in the calendars below, scores are always shown with your score first then the opponents.</p>

<?php
   foreach ($calendarSchedule as $monthSchedule) {
      echo br( 1 );
      echo $monthSchedule;
    }
?>
</div>

<?php
 }

/* ... store our buffered page in a variable that we can use if the user selects to mail the schedule */
$_SESSION['schedulePage'] = ob_get_contents();
$_SESSION['scheduleHeader'] = $scheduleHeader;

/* ... end output buffering */
ob_end_clean();

/* ... display the page we built */
echo $_SESSION['schedulePage'];
