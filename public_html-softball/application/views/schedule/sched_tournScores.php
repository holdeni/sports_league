<?php
// $Id: sched_tournScores.php 202 2011-08-23 17:02:14Z Henry $
// Last Change: $Date: 2011-08-23 13:02:14 -0400 (Tue, 23 Aug 2011) $

/* ... data declarations */
$todaysDate = date( "Y-m-d" );
?>

<h1><?= $scheduleHeader ?> (As of: <?= $todaysDate ?>)</h1>

<div id="explain">
<p>To report a game score, locate the game in the list provided and click on game details. On the resulting form, fill in the
   game score and press SUBMIT. If the game wasn't played for any reason, check the appropriate box and fill in the reason
   in the text box. Then press SUBMIT. 
</p>

<!--
<p>For games including results in the calendar below, scores are always shown with your score first then the opponents.
</p>
-->

</div>

<?php
/* ... determine if we are to show the form for submitting a game result */
if ($showResultsForm) {

/* ... for form presentation, we need to pad the shorter team name to be as long as the other one */
   $visitTeam = $this->Model_Team->getTeamName( $gameDetails['VisitTeamID'] );
   $homeTeam = $this->Model_Team->getTeamName( $gameDetails['HomeTeamID'] );
   $lenVisitTeam = strlen( $visitTeam );
   $lenHomeTeam = strlen( $homeTeam );
   $padReqd = max( $lenVisitTeam , $lenHomeTeam );
   $visitTeam = htmlspecialchars( $visitTeam );
   $homeTeam = htmlspecialchars( $homeTeam );
   if ($lenVisitTeam != $padReqd) {
      for ($i = 1; $i <= $padReqd - $lenVisitTeam; $i++) {
         $visitTeam .= "&nbsp;";
       }
    }
   else {
      for ($i = 1; $i <= $padReqd - $lenHomeTeam; $i++) {
         $homeTeam .= "&nbsp;";
       }
    }
   
/* ... setup attributes for the various fields we will be showing in the form */
   $attributes['Score'] = array(
      'maxlength'   => '3',
      'size' => '5',
    );
   $attributes['vScore'] = array(
      'name'        => 'visitScore',
      'id'          => 'visitScore',
      'value'       => set_value( "visitScore" ),
    );
   $attributes['l_vScore'] = array(
      'text' => $visitTeam.": ",
      'attributes' => array(
         'id' => 'l_visitScore',
         'class' => "fixedFont",
       ),
    );
   $attributes['hScore'] = array(
      'name'        => 'homeScore',
      'id'          => 'homeScore',
      'value'       => set_value( "homeScore" ),
    );
   $attributes['l_hScore'] = array(
      'text' => $homeTeam.": ",
      'attributes' => array(
         'id' => 'l_homeScore',
         'class' => "fixedFont",
       ),
    );
   $attributes['vScore'] = array_merge( $attributes['vScore'], $attributes['Score'] );
   $attributes['hScore'] = array_merge( $attributes['hScore'], $attributes['Score'] );

   $attributes['notPlayed'] = array(
      "name" => "notPlayed", 
      "id" => "notPlayed",
      "value" => "notPlayed",
      "checked" => $notPlayed ? TRUE : FALSE,
      "onClick" => "toggleElement( '#reason' ); toggleElement( '#visitScore' ); toggleElement( '#homeScore' ); 
                    toggleElement( '#l_visitScore' ); toggleElement( '#l_homeScore' ); toggleElement( '#l_Reason' );"
    );

   $attributes['Reason'] = array(
      "name" => "reason",
      "id" => "reason",
      "value" => set_value( "reason" ),
      "rows" => "5",
      "cols" => "40",
    );
   $attributes['l_Reason'] = array(
      'text' => "Provide reason(s) game was not played: ",
      'attributes' => array(
         'id' => 'l_Reason',
       ),
    );
?>

<div id="result-form">

<h2>Submitting Game Result For <?= $gameDetails['Date'] ?> at <?= $gameDetails['Time'] ?> on <?= $gameDetails['Diamond'] ?> </h2>

<?php
   echo form_open( "schedule/sched_tournament/processResults" );
   
   echo form_error( "visitScore", "<p class='error'>", "</p>" );
      
   echo form_label( $attributes['l_vScore']['text'], "visitScore", $attributes['l_vScore']['attributes'] );
   echo form_input( $attributes['vScore'] );
   echo br( 1 );
   
   echo form_error( "homeScore", "<p class='error'>", "</p>" );
      
   echo form_label( $attributes['l_hScore']['text'], "homeScore", $attributes['l_hScore']['attributes'] );
   echo form_input( $attributes['hScore'] );
   echo br( 2 );
   
   echo form_label( "If the game was NOT played, check this box to request a rescheduled game date: ", "notPlayed" );
   echo form_checkbox( $attributes['notPlayed'] );
   if ($notPlayed) {
      echo br( 2 );
    }
   else {
      echo br( 1 );
    }
      
   echo form_error( "reason", "<p class='error'>", "</p>" );
      
   echo form_label( $attributes['l_Reason']['text'], "reason", $attributes['l_Reason']['attributes'] );
   echo form_textarea( $attributes['Reason'] );
   echo br( 2 );
   
   echo form_submit( "submit", "Submit Results" );
      
   echo form_hidden( "gameID", $gameDetails['GameID'] );
   echo form_hidden( "gameYear", $Year );
   echo form_hidden( "gameMonth", $Month );
   
   echo form_close();
?>
</div>

<?php
 }
?>

<br />
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
         <th>Game Identifier</th>
         <th>Visiting Team</th>
         <th width="32px">&nbsp;</th>
         <th>Home Team</th>
         <th width="32px">&nbsp;</th>
         <th>Actions</th>
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
      if ($tournDetails[$i]['VisitTeamID'] != NULL) {
         $visitTeam = htmlspecialchars( $this->Model_Team->getTeamName( $tournDetails[$i]['VisitTeamID'] ) );
       }

/* ... determine what action the user may perform */
      $actionURL = "None available";
      if (($tournDetails[$i]['Status'] == "SCHEDULED"  ||  $tournDetails[$i]['Status'] == "RAINOUT")
          &&  ($tournDetails[$i]['HomeTeamID'] != ""  &&  $tournDetails[$i]['VisitTeamID'] != "")) {
         $actionURL = '<a href="'.base_url().'schedule/sched_tournament/reportScore/'.$tournDetails[$i]['GameID'].'">Report game result</a>';
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
         <td class="MC"><?= $actionURL ?></td>
      </tr>
   
<?php
      $lastGameDate = $gameDate;
    }
?>
   </table>
   </div>
   
</div>
