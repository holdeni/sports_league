<?php
// $Id: sched_rainouts.php 163 2011-06-08 13:41:10Z Henry $
// Last Change: $Date: 2011-06-08 09:41:10 -0400 (Wed, 08 Jun 2011) $

/* ... data declarations */
$todaysDate = date( "Y-m-d" );
$formFields = array();
$data = array();
$options = array();
$buttons = array();

/* ... build the brief form that is the list of rainout games as a drop-down select list */
$formFields[] = array(
   'fieldName' => "gameinfo",
   'fieldText' => "Rained Out Games",
   'required' => false,
   'type' => "dropdown",
   'default' => isset( $chosenGameID ) ? $chosenGameID : " ",
 );
 
$data['gameinfo'] = array(
   "id" => "gameinfo",
   "onChange" => 'form.submit();',
 );

$options['gameinfo'] = array( 
   " " => " ",
 );

foreach ($rainoutList as $gameID => $gameDetails) {
   $options['gameinfo'][$gameID] = $gameDetails['Date']." at ".$gameDetails['Time']." on ".$gameDetails['Diamond']." => ";
   $options['gameinfo'][$gameID] .= htmlspecialchars( $this->Model_Team->getTeamName( $gameDetails['VisitTeamID'] ) )." at ";
   $options['gameinfo'][$gameID] .= htmlspecialchars( $this->Model_Team->getTeamName( $gameDetails['HomeTeamID'] ) ) ;
 }
 
?>

<div id="reschedules">

<h1>Rainout Game Rescheduling (as of <?= $todaysDate ?>)</h1>

<p>Choose the game you wish to review for rescheduling from the following list of rained out games.
</p>

<?php
my_DisplayForm( $formFields, $options, $data, $buttons, "schedule/sched_rainouts/processGame");

if (isset( $chosenGameID )) {
?>
<br />

   <div id="reschedulingForm">

   <p>Below is the merged schedule showing the remaining games for both teams involved in the game being
      rescheduled along with the current open diamond slots. The team name is enclosed in []. To move a game
      to a specific <strong>[OPEN]</strong> slot, click on the link representing the slot you wish to use
      (you will have a chance to confirm the action before the 
      database is updated with any changes).
   </p>
   <p>You can use the selector above to change to a different game to consider rescheduling.
   </p>

      <div id="schedule-cal" class="schedule-cal">

<?php
   foreach ($commonCalendar as $monthSchedule) {
      echo br( 1 );
      echo $monthSchedule;
    }

?>
      </div>
   </div>

<?php
 }
?>

</div>
