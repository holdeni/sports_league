<?php

// $Id: sched_scores.php 184 2011-07-20 19:48:22Z Henry $
// Last Change: $Date: 2011-07-20 15:48:22 -0400 (Wed, 20 Jul 2011) $

class Sched_scores extends CI_Controller {

/*****
 * Function: Sched_scores (constructor)
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
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function index( $data = array() ) {

/* ... we can only proceed if the user is properly logged in */
      if (!$this->Model_Account->amILoggedIn()) {
         redirect( "mainpage/notLoggedIn", "refresh" );
       }

/* ... define values for template variables to display on page */
      if (!array_key_exists( "title", $data )) {
         $data['title'] = "Reporting Scores - ".$this->config->item( 'siteName' );
       }

/* ... set the name of the page to be displayed */
      if (!array_key_exists( "main", $data )) {
         $data['main'] = "schedule/sched_scores";
       }

/* ... if a captain views this, then we know which team's schedule to manage; if above a captain, */
/*     they'll have to choose which team */
      $data['scheduleHeader'] = "";
      if (array_key_exists( 'TeamID', $_SESSION )) {
         $data['scheduleHeader'] = "Team Schedule For ".htmlspecialchars( $this->Model_Team->getTeamName( $_SESSION['TeamID'] ) );
         $teamID = $_SESSION['TeamID'];
       }

/* ... prepare to display a month of the schedule */
      $this->load->library( 'calendar', $this->_setCalendarFormat() );
      if (!array_key_exists( "Year", $data )) {
         $data['Year'] = date( "Y" );
       }
      if (!array_key_exists( 'Month', $data )) {
         $data['Month'] = date( "m" );
       }

/* ... get the appropriate month of schedule data and then build the calendar page */
      $monthData = $this->_getMonthSchedule( $teamID, $data['Month'] );
      $data['monthSchedule'] = $this->calendar->generate( $data['Year'], $data['Month'], $monthData );

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
 * Function: updateCalendar
 *
 * Arguments:
 *    $year - Year for next calendar page
 *    $month - Month for next calendar page
 *
 * Returns:
 *    -none-
 *
 *****/
   function updateCalendar( $year = NULL, $month = NULL ) {

/* ... use the provided values or determine the default ones but update our data array */
      if ($year != "") {
         $data['Year'] = $year;
       }
      else {
         $data['Year'] = date( "Y" );
       }

      if ($month != "" ) {
         $data['Month'] = $month;
       }
      else {
         $data['Month'] = date( "m" );
       }

/* ... bounce over to the main routine for getting the next calendar page displayed */
      $this->index( $data );
      
/* ... time to go */
      return;
    }



/*****
 * Function: index
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function _getMonthSchedule( $teamID, $month ) {

/* ... data declarations */
      $schedule = array();
      $schedDetails = array();
      $monthData = array();
      
/* ... get the schedule details from the database */
      $schedDetails = $this->Model_Schedule->getScheduleDetails( $teamID, $month );

/* ... we need to break the data out by month and day of month in order to present information on calendar */
      for ($i = 0; $i < count( $schedDetails ); $i++) {
         $gameDate = explode( "-", $schedDetails[$i]['Date'] );
         $gmDay = (int) $gameDate[2];             // so we use a type cast;
         $schedule[$gmDay][] = $i;
       }

/* ... build a data structure for this month's games, a game at a time */
      foreach ($schedule as $day => $gameList) {
         
/* ... we may have more than one game on a day, so process them individually */
         foreach ($gameList as $index) {

/* ... start with the time and diamond details */            
            $dateInfo = $schedDetails[$index]['Time']." @ ".$schedDetails[$index]['Diamond'];
            
/* ... if it is a game without a results report yet, show who the opponent is/was */
            if ($schedDetails[$index]['Status'] == "SCHEDULED") {
               if ($teamID == $schedDetails[$index]['HomeTeamID']) {
                  $dateInfo .= " vs ".htmlspecialchars( $this->Model_Team->getTeamName( $schedDetails[$index]['VisitTeamID'] ) );
                }
               else {
                  $dateInfo .= " at ".htmlspecialchars( $this->Model_Team->getTeamName( $schedDetails[$index]['HomeTeamID'] ) );
                }
             }

/* ... if it is a rained out game that hasn't been rescheduled yet, list the opponent */
            elseif ($schedDetails[$index]['Status'] == "RAINOUT") {
               if ($teamID == $schedDetails[$index]['HomeTeamID']) {
                  $dateInfo .= " RAINOUT vs ".htmlspecialchars( $this->Model_Team->getTeamName( $schedDetails[$index]['VisitTeamID'] ) );
                }
               else {
                  $dateInfo .= " RAINOUT at ".htmlspecialchars( $this->Model_Team->getTeamName( $schedDetails[$index]['HomeTeamID'] ) );
                }
             }

/* ... if it is a game with a score reported, show these details */
            else {
               $gameResult = $this->Model_Schedule->gameResult( $schedDetails[$index]['GameID'] );
               if ($teamID == $schedDetails[$index]['HomeTeamID']) {
                  if ($gameResult['Result'] == "HOME") {
                     $dateInfo = "W (".$schedDetails[$index]['HomeScore']."-".$schedDetails[$index]['VisitScore'].") ";
                     $dateInfo .= "vs ".htmlspecialchars( $this->Model_Team->getTeamName( $schedDetails[$index]['VisitTeamID'] ) );
                   }
                  elseif ($gameResult['Result'] == "VISIT") {
                     $dateInfo = "L (".$schedDetails[$index]['HomeScore']."-".$schedDetails[$index]['VisitScore'].") ";
                     $dateInfo .= "vs ".htmlspecialchars( $this->Model_Team->getTeamName( $schedDetails[$index]['VisitTeamID'] ) );
                   }
                  else {
                     $dateInfo = "T (".$schedDetails[$index]['HomeScore']."-".$schedDetails[$index]['VisitScore'].") ";
                     $dateInfo .= "vs ".htmlspecialchars( $this->Model_Team->getTeamName( $schedDetails[$index]['VisitTeamID'] ) );
                   }
                }
               else {
                  if ($gameResult['Result'] == "VISIT") {
                     $dateInfo = "W (".$schedDetails[$index]['VisitScore']."-".$schedDetails[$index]['HomeScore'].") ";
                     $dateInfo .= "vs ".htmlspecialchars( $this->Model_Team->getTeamName( $schedDetails[$index]['HomeTeamID'] ) );
                   }
                  elseif ($gameResult['Result'] == "HOME") {
                     $dateInfo = "L (".$schedDetails[$index]['VisitScore']."-".$schedDetails[$index]['HomeScore'].") ";
                     $dateInfo .= "vs ".htmlspecialchars( $this->Model_Team->getTeamName( $schedDetails[$index]['HomeTeamID'] ) );
                   }
                  else {
                     $dateInfo = "T (".$schedDetails[$index]['HomeScore']."-".$schedDetails[$index]['VisitScore'].") ";
                     $dateInfo .= "vs ".htmlspecialchars( $this->Model_Team->getTeamName( $schedDetails[$index]['HomeTeamID'] ) );
                   }
                }
             }

/* ... save this game's detail after seeing if this is the first game for the day or do we add it to an already existing game entry */
            if (!isset( $monthData[$day] )) {
               $monthData[$day] = "<a href='".base_url()."schedule/sched_scores/report/".$schedDetails[$index]['GameID']."'>".$dateInfo."</a>";
             }
            else {
               $monthData[$day] .= "<br /><br />"."<a href='".base_url()."schedule/sched_scores/report/".$schedDetails[$index]['GameID']."'>".$dateInfo."</a>";
             }

          }
       }
       
/* ... time to go */
      return( $monthData );
    }



/*****
 * Function: report (Present form for user to report a game score)
 *
 * Arguments:
 *    $gameID - numeric unique identifier for game
 *    $prevSubmission - contents of form that was submitted (and contains errors)
 *
 * Returns:
 *    -none-
 *
 *****/
   function report( $gameID = NULL, $prevSubmission = array() ) {

/* ... if we weren't given a gameID upon invokation, we've been called improperly */
      if ($gameID == "") {
         echo "*** ERROR: function has been called improperly";
         exit;
       }

/* ... get the information on this game */
      $data['gameDetails'] = $this->Model_Schedule->getGameDetails( $gameID );
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
      $this->index( $data );
      
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
         $this->Model_Schedule->saveGameResult( $this->input->post( "gameID" ), $formData );

/* ... send an email out to the team contacts sharing the game results */
         $this->_emailTeams( $this->input->post( "gameID" ) );

/* ... a web trick that will popup a window for the user telling them their information was accepted */
?>
<script language="javascript">
   alert( "Game results saved in database. Results email issued." );
   window.close();
</script>
<?php

/* ... re-display the team's monthly calendar but it will now include this game's results */
         $this->updateCalendar( $this->input->post( "gameYear" ), $this->input->post( "gameMonth" ) );
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
            
         $this->report( $this->input->post( "gameID" ), $formData );
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
 *
 * Returns:
 *    -none-
 *
 *****/
   function _emailTeams( $gameID ) {

/* ... data declarations */
      $toAddr = array();
      $ccAddr = array();
      $bccAddr = array();
      
/* ... get the game details from the database */
      $gameDetails = $this->Model_Schedule->getGameDetails( $gameID );

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
      $body .= "If there is agreement to change the score, you can do so on the web, simply by re-entering the game results. ";
      $body .= "If teams cannot reach a mutual agreement on the score, please send an email to the-exec@kanatalobball.org providing ";
      $body .= "a synopsys of the disputed game result.\n";
      $body .= "Disagreements must be voiced within 3 days of this email otherwise this result will be considered official.\n";
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



/*****
 * Function: _setCalendarFormat (Define our calendar format)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    array of data used by CodeIgniter Calendar class to format calendar
 *
 *****/
   function _setCalendarFormat() {

/* ... we wish to have the ability to advance or retreat a month when displaying the calendar */
      $prefs['show_next_prev'] = TRUE;
      $prefs['next_prev_url'] = base_url()."schedule/sched_scores/updateCalendar/";

/* ... CodeIgniter's Calender class is very useful to us but the default format needs to be modified so that */
/*     we get calendars that contain information the way it is useful to us and our users */
      $prefs['template'] = '
      
         {table_open}<table border="border" cellpadding="0" cellspacing="0" class="calendar">{/table_open}
      
         {heading_row_start}<tr>{/heading_row_start}
      
         {heading_previous_cell}<th><a href="{previous_url}">&lt;&lt;</a></th>{/heading_previous_cell}
         {heading_title_cell}<th colspan="{colspan}">{heading}</th>{/heading_title_cell}
         {heading_next_cell}<th><a href="{next_url}">&gt;&gt;</a></th>{/heading_next_cell}
      
         {heading_row_end}</tr>{/heading_row_end}
      
         {week_row_start}<tr>{/week_row_start}
         {week_day_cell}<td class="MC">{week_day}</td>{/week_day_cell}
         {week_row_end}</tr>{/week_row_end}
      
			{cal_row_start}<tr class="days">{/cal_row_start}
			{cal_cell_start}<td class="day">{/cal_cell_start}
      
			{cal_cell_content}
				<div class="day_num">{day}</div>
				<br />
				<div class="content">{content}</div>
			{/cal_cell_content}
			{cal_cell_content_today}
				<div class="day_num">{day}</div>
				<br />
				<div class="content">{content}</div>
			{/cal_cell_content_today}
			
			{cal_cell_no_content}
			   <div class="day_num">{day}</div>
			{/cal_cell_no_content}
			{cal_cell_no_content_today}
			   <div class="day_num">{day}</div>
			{/cal_cell_no_content_today}
      
         {cal_cell_blank}&nbsp;{/cal_cell_blank}
      
         {cal_cell_end}</td>{/cal_cell_end}
         {cal_row_end}</tr>{/cal_row_end}
      
         {table_close}</table>{/table_close}
      ';

/* ... time to go */
      return( $prefs );
    }



 } /* ... end of Class */
