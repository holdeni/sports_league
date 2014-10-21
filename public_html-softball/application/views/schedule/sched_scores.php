<?php
// $Id: sched_scores.php 252 2012-08-01 15:16:59Z Henry $
// Last Change: $Date: 2012-08-01 11:16:59 -0400 (Wed, 01 Aug 2012) $

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
      'value'       => $gameDetails['VisitScore'],
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
      'value'       => $gameDetails['HomeScore'],
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

   $forfeiture_options = array(
   	"N" => "NO",
   	"V" => "Game was forfeited by ".htmlspecialchars( strtoupper( $this->Model_Team->getTeamName( $gameDetails['VisitTeamID']  ) ) ),
   	"H" => "Game was forfeited by ".htmlspecialchars( strtoupper( $this->Model_Team->getTeamName( $gameDetails['HomeTeamID'] ) ) ),
   );

   $attributes['forfeit'] = array(
   	"name" => "forfeit",
   	"id" => "forfeit",
   	"options" => $forfeiture_options,
   	"value" => $gameDetails['forfeit'],
    );
   $attributes['l_forfeit'] = array(
   	"text" => "Was game forfeited by either team? ",
   	'attributes' => array(
   		'id' => 'l_forfeit'
   	),
    );

   $attributes['rating'] = array(
   	"name" => "rating",
   	"id" => "rating",
   	"options" => $this->config->item( 'sportsmanship_ratings' ),
   	"value" => $gameDetails['rating'],
   );
   $attributes['l_rating'] = array(
   	'text' => "Sportsmanship Rating: ",
   	'attributes' => array(
   		'id' => 'l_rating'
   	),
   );
   $attributes['rating_text'] = array(
   	"name" => "rating_text",
   	"id" => "rating_text",
   	"rows" => "5",
   	"cols" => "40",
   	"value" => $gameDetails['rating_text'],
   );
   $attributes['l_rating_text'] = array(
   	'text' => "Sportsmanship Comments <br />",
   	'attributes' => array(
   		'id' => 'l_rating_text'
   	),
   );

   $attributes['comments'] = array(
   	"name" => "open_comments",
   	"id" => "open_comments",
   	"rows" => "5",
   	"cols" => "40",
   	"value" => $gameDetails['open_comments']
   );
   $attributes['l_Open_Comments'] = array(
   	'text' => "Other Game Comments <br />",
   	'attributes' => array(
   		'id' => 'l_Open_Comments'
   	),
   );

   $attributes['notPlayed'] = array(
      "name" => "notPlayed",
      "id" => "notPlayed",
      "value" => "notPlayed",
      "checked" => $notPlayed ? TRUE : FALSE,
      "onClick" => "toggleElement( '#reason' ); toggleElement( '#visitScore' ); toggleElement( '#homeScore' );
                    toggleElement( '#l_visitScore' ); toggleElement( '#l_homeScore' ); toggleElement( '#l_Reason' );
                    toggleElement( '#sportsmanship_div' ); toggleElement( '#open_comments_div' );
                    toggleElement( '#forfeit_div' );",
    );

   $attributes['Reason'] = array(
      "name" => "reason",
      "id" => "reason",
      "value" => set_value( "reason" ),
      "rows" => "5",
      "cols" => "40",
    );
   $attributes['l_Reason'] = array(
      'text' => "Provide reason(s) game was not played <br />",
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
?>
<div id='forfeit_div'>

<?php
   echo form_label( $attributes['l_forfeit']['text'], "forfeit", $attributes['l_forfeit']['attributes'] );
   echo form_dropdown( $attributes['forfeit']['id'], $attributes['forfeit']['options'], $attributes['forfeit']['value'] );
?>
<p>Please enter a score of <strong>7-0</strong> in favor of the team that did not forfeit.</p>
<br />
</div>

<?php
   echo form_label( "If the game was NOT played, check this box to request a rescheduled game date: ", "notPlayed" );
   echo form_checkbox( $attributes['notPlayed'] );
   if ($notPlayed) {
      echo br( 2 );
    }
   else {
      echo br( 2 );
    }

   echo form_error( "reason", "<p class='error'>", "</p>" );

   echo form_label( $attributes['l_Reason']['text'], "reason", $attributes['l_Reason']['attributes'] );
   echo form_textarea( $attributes['Reason'] );
   echo br( 1 );
?>

<div id='sportsmanship_div'>
<h3>Sportsmanship Rating</h3>

<p>Please use the rating to provide input as to the level of sportsmanship you experienced during this game by your opponent. You may also provide
	additional comments to explain your rating if you so desire. The rating and comments will not be seen by the other team but only by appropriate
	league executive members.
</p>
<p>You must provide a rating but comments are optional.</p>

<?php
   echo form_error( "rating", "<p class='error'>", "</p>" );

	echo form_label( $attributes['l_rating']['text'], "rating", $attributes['l_rating']['attributes'] );
	echo form_dropdown( $attributes['rating']['id'], $attributes['rating']['options'], $attributes['rating']['value'] );
	echo br( 1 );
	echo form_label( $attributes['l_rating_text']['text'], "rating_text", $attributes['l_rating_text']['attributes'] );
	echo form_textarea( $attributes['rating_text'] );
	echo br( 1 );

?>
</div>
<div id='open_comments_div'>
<h3>Other Game Comments</h3>

<p>Please provide us any additional comments about the game. These comments <strong>will be shared</strong> with your opponents. Comments here can
	be about a game incident unrelated to sportsmanship or to report a problem with the field conditions. As appropriate, the league executive will
	follow-up on comments.
</p>

<?php
	echo form_label( $attributes['l_Open_Comments']['text'], "comments", $attributes['l_Open_Comments']['attributes'] );
	echo form_textarea( $attributes['comments'] );
?>
</div>

<?php
   if ($notPlayed) {
      echo br( 2 );
    }
   else {
      echo br( 1 );
    }
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
<br />
<br />

<div id="schedule-cal">

<?php
   echo $monthSchedule;
?>
</div>

