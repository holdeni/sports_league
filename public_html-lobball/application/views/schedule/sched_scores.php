<?php
// $Id: sched_scores.php 84 2011-04-13 19:22:44Z Henry $
// Last Change: $Date: 2011-04-13 15:22:44 -0400 (Wed, 13 Apr 2011) $

/* ... data declarations */
$todaysDate = date( "Y-m-d" );
?>

<h1><?= $scheduleHeader ?> (As of: <?= $todaysDate ?>)</h1>

<div id="explain">
<p>To report a game score, locate the game on the calendar and click on game details. On the resulting form, fill in the
   game score and press SUBMIT. If the game wasn't played for any reason, check the appropriate box and fill in the reason
   in the text box. Then press SUBMIT. 
</p>

<p>For games including results in the calendar below, scores are always shown with your score first then the opponents.
</p>

<p>Use &lt;&lt; and &gt;&gt; on the calendar titlebar to change the month displayed.
</p>
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
   echo form_open( "schedule/sched_scores/processResults" );
   
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
<div id="schedule-cal">

<?php
   echo $monthSchedule;
?>
</div>

