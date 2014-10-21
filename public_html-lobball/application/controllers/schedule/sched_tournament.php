<?php

// $Id: sched_tournament.php 202 2011-08-23 17:02:14Z Henry $
// Last Change: $Date: 2011-08-23 13:02:14 -0400 (Tue, 23 Aug 2011) $

class Sched_tournament extends CI_Controller {

/*****
 * Function: (constructor)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function __construct() {
      parent::__construct();
      session_start();
    }



/*****
 * Function: index
 *
 * Arguments:
 *    -none
 *
 * Returns:
 *    -none-
 *
 *****/
   function index( $data = array() ) {

/* ... define values for template variables to display on page */
      if (!array_key_exists( "title", $data )) {
         $data['title'] = "Playoffs - ".$this->config->item( 'siteName' );
       }

/* ... set the name of the page to be displayed */
      if (!array_key_exists( "main", $data )) {
         $data['main'] = "schedule/sched_tournamentMaster";
       }

/* ... determine which view for the small left hand contextual navigation menu */
      if (array_key_exists( 'UserId', $_SESSION )) {
         $data['contextNav'] = "loggedIn";
       }
      else {
         $data['contextNav'] = "loggedOut";
       }

/* ... enable our template variables and then display the template, as we want it shown */      
      $this->load->vars( $data );
      $this->load->view( "template" );

/* ... time to go */
      return;
    }



/*****
 * Function: showGames (Show games involved in double elim tournament)
 *
 * Arguments:
 *    $curDivision - if set, specifies division to show the games about
 *
 * Returns:
 *    -none-
 *
 *****/
   function showGames( $curDivision="" ) {

/* ... define values for template variables to display on page */
      if ($curDivision == "") {
         $data['title'] = "Playoffs - ".$this->config->item( 'siteName' );
       }
      else {
         $data['title'] = "Division ".$curDivision." Playoffs - ".$this->config->item( 'siteName' );
       }

/* ... get the schedule details from the database */ 
      if ($curDivision == "") {     
         $data['tournDetails'] = $this->Model_Tournament->getAllDetails();
         $data['tournHeader'] = "League Playoff Schedule";
       }
      else {
         $data['tournDetails'] = $this->Model_Tournament->getDivDetails( $curDivision );
         $data['tournHeader'] = "Division ".$curDivision." Playoff Schedule";
       }

/* ... replace seeding placeholders in the schedule with appropriate team ids */
      for ($i=0; $i < count( $data['tournDetails'] ); $i++) {

         if ($data['tournDetails'][$i]['Status'] == "SCHEDULED") {
            if ($curDivision == "") {
               $seedDiv = substr( $data['tournDetails'][$i]['TournamentID'], 0, 1 );
             }
            else {
               $seedDiv = $curDivision;
             }
            if ($data['tournDetails'][$i]['HomeTeamID'] == NULL  &&  $data['tournDetails'][$i]['HomeSeed'] != NULL) {
               $teamID = $this->Model_Tournament->getSeedTeamID( $data['tournDetails'][$i]['HomeSeed'], $seedDiv );
               $data['tournDetails'][$i]['HomeTeamID'] = $teamID > 0 ? $teamID : "";
             }
            if ($data['tournDetails'][$i]['VisitTeamID'] == NULL  &&  $data['tournDetails'][$i]['VisitSeed'] != NULL) {
               $teamID = $this->Model_Tournament->getSeedTeamID( $data['tournDetails'][$i]['VisitSeed'], $seedDiv );
               $data['tournDetails'][$i]['VisitTeamID'] = $teamID > 0 ? $teamID : "";
             }
          }
       }
       
/* ... set the name of the page to be displayed */
      $data['main'] = "schedule/sched_tournamentMaster";

      $this->index( $data );
      
/* ... time to go */
      return;
    }



/*****
 * Function: report (List tournament games for current user's team)
 *
 * Arguments:
 *    $data - possible pre-loaded data for the page
 *
 * Returns:
 *    -none-
 *
 *****/
   function report( $data = array() ) {

/* ... data declarations */
      $teamID = "";
      $data['tournDetails'] = array();

/* ... ensure user is properly logged in to the site */
      if (!$this->Model_Account->amILoggedIn()) {
         redirect( "mainpage/notLoggedIn", "refresh" );
       }

/* ... determine their team affiliation and get the games their team are involved in */
      $data['scheduleHeader'] = "";
      if (array_key_exists( 'TeamID', $_SESSION )) {
         $data['scheduleHeader'] = "Playoff Schedule For ".htmlspecialchars( $this->Model_Team->getTeamName( $_SESSION['TeamID'] ) );
         $teamID = $_SESSION['TeamID'];
         $data['tournDetails'] = $this->Model_Tournament->getTeamSchedule( $teamID );
       }
      
/* ... define values for template variables to display on page */
      $data['title'] = "Reporting Playoff Scores - ".$this->config->item( 'siteName' );

/* ... set the name of the page to be displayed */
      $data['main'] = "schedule/sched_tournScores";

/* ... set the variable that controls whether we display the form go get the results of a game */
      if (!array_key_exists( 'showResultsForm', $data )) {
         $data['showResultsForm'] = false;
       }

/* ... determine which view for the small left hand contextual navigation menu */
      if (array_key_exists( 'UserId', $_SESSION )) {
         $data['contextNav'] = "loggedIn";
       }
      else {
         $data['contextNav'] = "loggedOut";
       }

/* ... enable our template variables and then display the template, as we want it shown */      
      $this->load->vars( $data );
      $this->load->view( "template" );

/* ... time to go */
      return;
    }



/*****
 * Function: reportScore (Present form for user to report a game score)
 *
 * Arguments:
 *    $gameID - numeric unique identifier for game
 *    $prevSubmission - contents of form that was submitted (and contains errors)
 *
 * Returns:
 *    -none-
 *
 *****/
   function reportScore( $gameID = NULL, $prevSubmission = array() ) {

/* ... if we weren't given a gameID upon invokation, we've been called improperly */
      if ($gameID == "") {
         echo "*** ERROR: function has been called improperly";
         exit;
       }

/* ... get the information on this game */
      $data['gameDetails'] = $this->Model_Tournament->getGameDetails( $gameID );
      $gameDate = explode( "-", $data['gameDetails']['Date'] );
      $data['Year'] = (int) $gameDate[0];
      $data['Month'] = (int) $gameDate[1];
      $data['showResultsForm'] = true;

/* ... see if we are re-drawing the form, so we can reset the form as we left it -- or is this the 1st time to show it */
/* ... the main thing about this form is the setting for the Rainout / Reschedule checkbox; by default we assume the game */
/*     was played so we want the team scores; but if we've been through the form once and the box was checked, then we wish to */
/*     return to the form with the box still checked */
      if (array_key_exists( "notPlayed", $prevSubmission )) {
         $data['notPlayed'] = $prevSubmission['notPlayed'];
       }
      else {
         $data['notPlayed'] = FALSE;
       }

/* ... determine what set of fields are hidden from view when we show the form */
      if ($data['notPlayed']) {
         $data['pageJavaScript'] = "$(document).ready( function() { hideElement( '#visitScore' ); hideElement( '#l_visitScore' ); 
            hideElement( '#homeScore' ); hideElement( '#l_homeScore' ) } )\n";
       }
      else {
         $data['pageJavaScript'] = "$(document).ready( function() { hideElement( '#reason' ); hideElement( '#l_Reason' ); } )\n";
       }

/* ... pass this info back to the routine that does our display */
      $this->report( $data );
      
/* ... time to go */
      return;
    }



/*****
 * Function: processResults (Process contents from form about a game result)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function processResults() {

/* ... setup validation on the basic data the form should have provided us; our validation rules vary depending whether */
/*     the game was played or not */
      $this->load->library( 'form_validation' );
      if ($this->input->post( "notPlayed" ) != "notPlayed") {
         $gamePlayed = true;
         $this->form_validation->set_rules( "visitScore", "Visiting team's score", "trim|min_length[1]|max_length[2]|integer|required|callback_positiveIntCheck" );
         $this->form_validation->set_rules( "homeScore", "Home team's score", "trim|min_length[1]|max_length[2]|integer|required|callback_positiveIntCheck" );
       }
      else {
         $gamePlayed = false;
         $this->form_validation->set_rules( "reason", "Reason", "min_length[5]|max_length[256]|required" );
       }

/* ... validate the form contents */
      if ($this->form_validation->run()) {

/* ... determine what game information we need to save to the database - played game or a rainout */
         if ($gamePlayed) {
            $formData = array( 
               "HomeScore" => $this->input->post( "homeScore" ), 
               "VisitScore" => $this->input->post( "visitScore" ),
               "Status" => "PLAYED",
               "Notes" => NULL,
             );
          }
         else {
            $formData = array( 
               "Status" => "RAINOUT",
               "Notes" => $this->input->post( "reason" ),
               "HomeScore" => NULL, 
               "VisitScore" => NULL,
             );
          }
         $this->Model_Tournament->saveGameResult( $this->input->post( "gameID" ), $formData );

/* ... if the game was played, we need to place the winner and maybe the loser in their next game */
         $nextGame = array();
         if ($gamePlayed  &&  $formData['VisitScore'] != $formData['HomeScore']) {
            $gameDetails = $this->Model_Tournament->getGameDetails( $this->input->post( "gameID" ) );
            if ($formData['VisitScore'] > $formData['HomeScore']) {
               $winTeam = $gameDetails['VisitTeamID'];
               $loseTeam = $gameDetails['HomeTeamID'];
             }
            else {
               $winTeam = $gameDetails['HomeTeamID'];
               $loseTeam = $gameDetails['VisitTeamID'];
             }
            $nextGame['Winner'] = $this->Model_Tournament->determineNextGame( $this->input->post( "gameID" ), $winTeam, "WINNER" );
            $nextGame['Loser'] = $this->Model_Tournament->determineNextGame( $this->input->post( "gameID" ), $loseTeam, "LOSER" );
          }

/* ... send an email out to the team contacts sharing the game results */
         $this->_emailTeams( $this->input->post( "gameID" ), $nextGame );

/* ... a web trick that will popup a window for the user telling them their information was accepted */
?>
<script language="javascript">
   alert( "Game results saved in database. Results email issued." );
   window.close();
</script>
<?php

/* ... re-display the team's tournament schedule now include this game's results */
         $this->report();
       }
      else {

/* ... determine what data or state from this form submission we need to pass back for the form re-draw; */
/* ... at this time, we need to know whether this was a played gamed or not, to set the checkbox on the form */
         if ($gamePlayed) {
            $formData = array(
               "notPlayed" => FALSE,
             );
          }
         else {
            $formData = array(
               "notPlayed" => TRUE,
             );
          }
            
         $this->reportScore( $this->input->post( "gameID" ), $formData );
       }

/* ... time to go */
      return;
    }



/*****
 * Function: processResults (Process contents from form about a game result)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function positiveIntCheck( $value ) {

/* ... is this value greater than or equal to zero or not? */
      if ($value >= 0) {
         $testValue = TRUE;
       }
      else {
         $testValue = FALSE;
         $this->form_validation->set_message('positiveIntCheck', "The %s field must be equal to or greater than zero");
       }
      
/* ... time to go */
      return( $testValue );
    }



/*****
 * Function: _emailTeams (Send an email to teams about recent game result)
 *
 * Arguments:
 *    $gameID - numeric game ID for game to be emailed about
 *    $nextGames - array of numeric game IDs for both teams involved in current game
 *
 * Returns:
 *    -none-
 *
 *****/
   function _emailTeams( $gameID, $nextGames ) {

/* ... data declarations */
      $toAddr = array();
      $ccAddr = array();
      $bccAddr = array();
      
/* ... get the game details from the database */
      $gameDetails = $this->Model_Tournament->getGameDetails( $gameID );

/* ... if for some reason we didn't retrieve a game from the database, bail out now */
      if (!array_key_exists( 'GameID', $gameDetails )) {
         print "*** ERROR: Attempt to email teams about Game ID ".$gameID." failed.\n";
         exit;
       }
       
/* ... we need to get the team contacts for the 2 teams that played */
      $visitEmails = $this->Model_Team->buildTeamMailingList( $gameDetails['VisitTeamID'] );
      $homeEmails = $this->Model_Team->buildTeamMailingList( $gameDetails['HomeTeamID'] );
      $toAddr = array_merge( $visitEmails, $homeEmails );

/* ... start with the standard message information */
      $body = "This email has been sent by an automated process and does not require a reply.\n\n";

/* ... now build the body of the email message */
      if ($gameDetails['Status'] == "PLAYED") {
         $body .= "Game result submission\n";
         $body .= "======================\n\n";
         $body .= "Date: ".$gameDetails['Date']." Time: ".$gameDetails['Time']." Diamond: ".$gameDetails['Diamond']."\n\n";
         $body .= $this->Model_Team->getTeamName( $gameDetails['VisitTeamID'] ).": ".$gameDetails['VisitScore']." at ";
         $body .= $this->Model_Team->getTeamName( $gameDetails['HomeTeamID'] ).": ".$gameDetails['HomeScore']."\n";

/* ... include some information about next games */
         $nextGameDetails = $this->Model_Tournament->getGameDetails( $nextGames['Winner'] );
         $body .= "\n";
         $body .= "Winner of this game next plays ".$nextGameDetails['Date']." at ".$nextGameDetails['Time']." on ".$nextGameDetails['Diamond']."\n";
         if ($nextGames['Loser'] != -1) {
            $nextGameDetails = $this->Model_Tournament->getGameDetails( $nextGames['Loser'] );
            $body .= "\n";
            $body .= "Loser of this game next plays ".$nextGameDetails['Date']." at ".$nextGameDetails['Time']." on ".$nextGameDetails['Diamond']."\n";
          }
         else {
            $body .= "Loser of this game has no further games to play\n";
          }
         $body .= "\nThe schedule on the web has been updated with the results and future game assignments.\n";
         
       }
      elseif ($gameDetails['Status'] == "RAINOUT") {
         $body .= "RAINOUT Game Report\n";
         $body .= "===================\n\n"; 
         $body .= "Date: ".$gameDetails['Date']." Time: ".$gameDetails['Time']." Diamond: ".$gameDetails['Diamond']."\n\n";
         $body .= $this->Model_Team->getTeamName( $gameDetails['VisitTeamID'] )." at ".$this->Model_Team->getTeamName( $gameDetails['HomeTeamID'] )."\n";
         $body .= "Reason for reschedule request\n";
         $body .= $gameDetails['Notes']."\n";
         $body .= "\n\nIMPORTANT: Both teams will be informed when this game has been reschdeduled\n";
         $ccAddr = array( $this->config->item( 'my_execAddress' ) );
       }

/* ... put in the standard details about disagreements */
      $body .= "\n-------------------------\n";
      $body .= "If there is a disagreement about this game report, team captains should contact each other and discuss their concerns. ";
      $body .= "If there is agreement to change the score, you must contact webmaster@kanatalobball.org with details of the change. ";
      $body .= "If teams cannot reach a mutual agreement on the score, please send an email to the-exec@kanatalobball.org providing ";
      $body .= "a synopsys of the disputed game result.\n";
      $body .= "Disagreements must be voiced within 1 day of this email otherwise this result will be considered official.\n";
      $body .= "\n";
      $body .= "If you have concerns about the process for reporting games, please send them in an email to webmaster@kanatalobball.org\n";
      $body .= "\n\n";
      

/* ... we need a subject line */
      $subject = "[Kanata Lobball] Game report for ".$gameDetails['Date']." @ ".$gameDetails['Time']." on ".$gameDetails['Diamond']." [".$gameDetails['GameID']."]";

/* ... send the email message */
      $this->Model_Mail->sendTextEmail( $toAddr, $ccAddr, $bccAddr, $subject, $body );
      
/* ... time to go */
      return;
    }



 } /* ... end of Controller */
