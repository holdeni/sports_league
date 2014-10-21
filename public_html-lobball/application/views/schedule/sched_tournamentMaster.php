<?php
// $Id: sched_tournamentMaster.php 196 2011-08-07 14:58:40Z Henry $
// Last Change: $Date: 2011-08-07 10:58:40 -0400 (Sun, 07 Aug 2011) $

/* ... data declarations */
$todaysDate = date( "Y-m-d" );

if (FALSE) {
/* ... if we are logged in, offer the user an option to have the schedule emailed to them */
if ($this->Model_Account->amILoggedIn()) {
?>   
   <div id="emailSchedule">

<?php
   echo form_open( "schedule/sched_tournament/emailtourn" );
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
} 

/* ... start buffering the output (since we may display it on the screen or email it */
ob_start();
?>

<h1><?= $tournHeader ?> (As of: <?= $todaysDate ?>)</h1>

<div>
   <p>Home team is always the team with the lower seed (better final position in the standings), except for the final games. In the 1st
      final game, the home team is the team without any losses. In the 2nd game, if required, the home team switches to the team which
      won the 1st final game.
   </p>
   <p>Home and visiting team assignments in the schedule below may change as game results are submitted. If only one team is known for a game, it will automatically
      be listed as the home team. When their opponent is known, home and visiting team determination will follow the process described above.
   </p>
</div>

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
         <th>Game Identifier</th>
         <th>Visiting Team</th>
         <th width="32px">&nbsp;</th>
         <th>Home Team</th>
         <th width="32px">&nbsp;</th>
         <th>Winner's Next Game</th>
         <th>Loser's Next Game</th>
      </tr>
   
<?php
/* ... cycle through the schedule and display all our entries */
   $lastGameDate = "";
   for ($i = 0; $i < count( $tournDetails ); $i++) {

/* ... format the date and determine if we are showing a different date from the last we showed */
      $gameDate = date( "D, M d Y", strtotime( $tournDetails[$i]['Date'] ) );
      $newGameDate = $gameDate != $lastGameDate ? true : false;

/* ... prepare the home and visiting team information */
      $visitTeam = "";
      $homeTeam = "";
      if ($tournDetails[$i]['HomeTeamID'] != NULL) {
         $homeTeam = htmlspecialchars( $this->Model_Team->getTeamName( $tournDetails[$i]['HomeTeamID'] ) );
       }
      elseif ($tournDetails[$i]['HomeSeed'] != NULL) {
         $homeTeam = "Seed ".$tournDetails[$i]['HomeSeed'];
       }
      if ($tournDetails[$i]['VisitTeamID'] != NULL) {
         $visitTeam = htmlspecialchars( $this->Model_Team->getTeamName( $tournDetails[$i]['VisitTeamID'] ) );
       }
      elseif ($tournDetails[$i]['VisitSeed'] != NULL) {
         $visitTeam = "Seed ".$tournDetails[$i]['VisitSeed'];
       }
?>
      <tr class="<?= $newGameDate ? "newDay" : "" ?>
                 <?= $tournDetails[$i]['Status'] == "PLAYED" ? "played" : "" ?>
                 <?= $tournDetails[$i]['Status'] == "RAINOUT" ? "rainout" : "" ?>">
         <td class="ML"><?= $newGameDate ? $gameDate : "" ?></td>
         <td class="MC"><?= $tournDetails[$i]['Time'] ?></td>
         <td class="MC"><?= ucfirst( strtolower( $tournDetails[$i]['Diamond'] ) ) ?></td>
         <td class="MC"><?= $tournDetails[$i]['TournamentID'] ?></td>
         <td class="ML"><?= $visitTeam ?></td>
         <td class="MC"><?= $tournDetails[$i]['VisitScore'] ?></td>
         <td class="ML"><?= $homeTeam ?></td>
         <td class="MC"><?= $tournDetails[$i]['HomeScore'] ?></td>
         <td class="MC"><?= $tournDetails[$i]['WinnerNextGame'] ?></td>
         <td class="MC"><?= $tournDetails[$i]['LoserNextGame'] ?></td>
      </tr>
   
<?php
      $lastGameDate = $gameDate;
    }
?>
   </table>
   </div>
   
</div>

<?php
/* ... store our buffered page in a variable that we can use if the user selects to mail the schedule */
$_SESSION['tournPage'] = ob_get_contents();
$_SESSION['tournHeader'] = $tournHeader;

/* ... end output buffering */
ob_end_clean();

/* ... display the page we built */
echo $_SESSION['tournPage'];
