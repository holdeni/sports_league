<?php

// $Id: sched_rainouts.php 147 2011-05-26 17:52:39Z Henry $
// Last Change: $Date: 2011-05-26 13:52:39 -0400 (Thu, 26 May 2011) $

class Sched_rainouts extends CI_Controller {

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
 *    $data - associative array containing some or all values for page display; default values used if not provided
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
         $data['title'] = "Game Rescheduling - ".$this->config->item( 'siteName' );
       }

/* ... build the form listing rainouts requiring rescheduling */
      $data['rainoutList'] = $this->_buildRainoutListForm();
      
/* ... set the name of the page to be displayed */
      if (!array_key_exists( "main", $data )) {
         $data['main'] = "schedule/sched_rainouts";
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
 * Function: _buildRainoutListForm (Build a form so rainout games can be selected)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    associative array with simple form instructions
 *
 *****/
   function _buildRainoutListForm() {

/* ... data declarations */
      $rainoutGames = array();
      
/* ... get the list of rainout games */
      $rainoutList = $this->Model_Schedule->getRainoutGames();

/* ... get pertinent details on the rainout games */
      foreach ($rainoutList as $gameID) {
         $gameDetails = $this->Model_Schedule->getGameDetails( $gameID );
         $rainoutGames[$gameID]['Date'] = $gameDetails['Date'];
         $rainoutGames[$gameID]['Time'] = $gameDetails['Time'];
         $rainoutGames[$gameID]['Diamond'] = $gameDetails['Diamond'];
         $rainoutGames[$gameID]['VisitTeamID'] = $gameDetails['VisitTeamID'];
         $rainoutGames[$gameID]['HomeTeamID'] = $gameDetails['HomeTeamID'];
       }
      
/* ... time to go */
      return( $rainoutGames );
    }



/*****
 * Function: processGame (Process a rainout rescheduling)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function processGame() {

/* ... data declarations */
      $data['homeSchedule'] = array();
      $data['visitSchedule'] = array();
      
/* ... determine which game ID was selected */
      $data['chosenGameID'] = $this->input->post( 'gameinfo' );
      $_SESSION['rainoutGameID'] = $data['chosenGameID'];

/* ... get the remaining schedule for both teams involved in the rainout */
      $gameDetails = $this->Model_Schedule->getGameDetails( $data['chosenGameID'] );
      $teamsSchedule = $this->Model_Schedule->getRemainingGames( array( $gameDetails['HomeTeamID'], $gameDetails['VisitTeamID'] ) );

/* ... get the list of open dates available to us */
      $openSlots = $this->Model_Schedule->getOpenSlots();

/* ... build the common schedule */
      $data['commonCalendar'] = $this->_buildCalendar( $teamsSchedule, $openSlots,
                                                       array( $gameDetails['HomeTeamID'], $gameDetails['VisitTeamID'] ) );
      
/* ... loop back to the main page routine */
      $this->index( $data );
      
/* ... time to go */
      return;
    }



/*****
 * Function: _buildCalendar (Build a monthly calendars merged with both team's schedules and open diamond slots)
 *
 * Arguments:
 *    $schedDetails - merged schedules of 2 teams involved
 *    $teamList - array of 2 team IDs involved
 *
 * Returns:
 *    -none-
 *
 *****/
   function _buildCalendar( $schedDetails, $openSlots, $teamList ) {

/* ... time to prepare for our alternative format of the schedule */
      $this->load->library( 'calendar', $this->_setCalendarFormat() );

/* ... initialize our structure to keep track of which months we have games scheduled in */
      for ($i = 1; $i <= 12; $i++) {
         $monthUsed[$i] = false;
       }

/* ... we need to break the 2 teams' schedules data out by month and day of month in order to present information on calendar */
      for ($i = 0; $i < count( $schedDetails ); $i++) {
         $gameDate = explode( "-", $schedDetails[$i]['Date'] );
         $gmMonth = (int) $gameDate[1];           // We need to convert from a string type to an integer type,
         $gmDay = (int) $gameDate[2];             // so we use a type cast;
         $schedule[$gmMonth][$gmDay][] = $i;
         $monthUsed[$gmMonth] = true;
       }

      for ($i = 0; $i < count( $openSlots ); $i++) {
         $gameDate = explode( "-", $openSlots[$i]['Date'] );
         $gmMonth = (int) $gameDate[1];           // We need to convert from a string type to an integer type,
         $gmDay = (int) $gameDate[2];             // so we use a type cast;
         $freeTime[$gmMonth][$gmDay][] = $i;
         $monthUsed[$gmMonth] = true;
       }

/* ... if we have games in a month, collect the game details in that month and display the calendar */
      for ($i = 1; $i <= 12; $i++) {

         if ($monthUsed[$i]) {

/* ... build a data structure for that month's games, a game at a time */
            $monthData = array();
            foreach ($schedule[$i] as $day => $gameList) {

/* ... we may have multiple games in a day, so we need to handle each one in turn */
               foreach ($gameList as $index) {

/* ... figure out which team's game we are processing */
                  if (in_array( $schedDetails[$index]['HomeTeamID'], $teamList )  &&  in_array( $schedDetails[$index]['VisitTeamID'], $teamList )) {
                     $teamName = "BOTH";
                   }
                  elseif (in_array( $schedDetails[$index]['HomeTeamID'], $teamList )) {
                     $teamName = htmlspecialchars( $this->Model_Team->getTeamName( $schedDetails[$index]['HomeTeamID'] ) );
                   }
                  else {
                     $teamName = htmlspecialchars( $this->Model_Team->getTeamName( $schedDetails[$index]['VisitTeamID'] ) );
                   }

/* ... prepare the game entry */
                  $dateInfo = "[".$teamName."] ".$schedDetails[$index]['Time']." @ ".$schedDetails[$index]['Diamond'];

/* ... save this game's detail after seeing if this is the first game for the day or do we add it to an already existing game entry */
                  if (!isset( $monthData[$day] )) {
                     $monthData[$day] = $dateInfo;
                   }
                  else {
                     $monthData[$day] .= "<br /><br />".$dateInfo;
                   }
                }
             }

/* ... now we need to process in the open slots */
            if (isset( $freeTime[$i] ) ) {
               foreach ($freeTime[$i] as $day => $gameList) {

/* ... we may have multiple games in a day, so we need to handle each one in turn */
                  foreach ($gameList as $index) {

/* ... prepare the entry */
                    $dateInfo = anchor( "schedule/sched_rainouts/moveGame/".$openSlots[$index]['GameID'],
                                         '[OPEN] '.$openSlots[$index]['Time'].' @ '.$openSlots[$index]['Diamond'] );

/* ... save this detail after seeing if this is the first for the day or do we add it to an already existing entry */
                     if (!isset( $monthData[$day] )) {
                        $monthData[$day] = $dateInfo;
                      }
                     else {
                        $monthData[$day] .= "<br /><br />".$dateInfo;
                      }
                   }
                }
             }

/* ... make the month's calendar */
            $calendarSchedule[$i] = $this->calendar->generate( $this->config->item( 'thisYear' ), $i, $monthData );

          }
       }

/* ... time to go */
      return( $calendarSchedule );
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



/*****
 * Function: moveGame (Move a rained out game to a new date)
 *
 * Arguments:
 *    $newDate - date to move game to
 *    $newtime - time on the date to move game to
 *    $newDiamond - diamond to use on date at the time
 *
 * Returns:
 *    -none-
 *
 *****/
   function moveGame( $newGameSlot ) {

/* ... data declarations */
      $data['chosenGameID'] = $_SESSION['rainoutGameID'];

/* ... get the details on the original game */
      $data['oldGameDetails'] = $this->Model_Schedule->getGameDetails( $data['chosenGameID'] );

/* ... bundle the details on the new slot */
//      $data['newGameDetails'] = split( "/", urldecode( $newGameSlot ) );
      $data['newGameDetails'] = $this->Model_Schedule->getGameDetails( $newGameSlot );

/* ... loop back to the main page routine */
      $data['main'] = 'schedule/sched_moveGame';
      $this->index( $data );
      
/* ... time to go */
      return;
    }



/*****
 * Function: processConfirmation (See if moving of game should or should not proceed)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function processConfirmation() {

/* ... pretty simple - if the user selected "YES", then do the move; otherwise get the heck out of Dodge! */
      if ($this->input->post( 'confirm' ) == "YES") {
          $this->_processGameChange( $this->input->post( 'oldGameID' ), $this->input->post( 'newGameID' ) );
       }
      else {
         redirect( "schedule/sched_rainouts/index", "refresh" );
       }

/* ... time to go */
      return;
    }



/*****
 * Function: _processGameChange (Proceed with actual changes for a game)
 *
 * Arguments:
 *    $oldGameID - numeric ID for game to be moved
 *    $newGameID - numeric ID for new slot game is to occupy
 *
 * Returns:
 *    -none-
 *
 *****/
   function _processGameChange( $oldGameID, $newGameID ) {

/* ... get the information on the existing game */
      $oldGameDetails = $this->Model_Schedule->getGameDetails( $oldGameID );
      
/* ... confirm the new date, time and diamond are available */
      $newGameSlot = $this->Model_Schedule->getGameDetails( $newGameID );
      if ($newGameSlot != "OPEN") {
         // do something about trying to use an occupied slot
       }

/* ... change the open date to a scheduled game */
      $newGameSlot['HomeTeamID'] = $oldGameDetails['HomeTeamID'];
      $newGameSlot['VisitTeamID'] = $oldGameDetails['VisitTeamID'];
      $newGameSlot['Status'] = "SCHEDULED";
      $newGameSlot['StatusNotes'] = "RAINOUT from GameID: ".$oldGameDetails['GameID']." (".$oldGameDetails['Date']." ".$oldGameDetails['Time']." ".$oldGameDetails['Diamond'].")";
      $this->Model_Schedule->saveGameResult( $newGameID, $newGameSlot );

/* ... change the original game to a show it has been rescheduled */
      $oldGameDetails['Status'] = "RESCHEDULED";
      $oldGameDetails['StatusNotes'] = "MOVED to GameID: ".$newGameSlot['GameID']." (".$newGameSlot['Date']." ".$newGameSlot['Time']." ".$newGameSlot['Diamond'].")";
      $this->Model_Schedule->saveGameResult( $oldGameID, $oldGameDetails );

/* ... send emails to both teams indicating their game has a new date, time and diamond assignment */
      $toAddr = array();
      $vAddr = $this->Model_Team->buildTeamMailingList( $oldGameDetails['VisitTeamID'] );
      $hAddr = $this->Model_Team->buildTeamMailingList( $oldGameDetails['HomeTeamID'] );
      $toAddr = array_merge( $vAddr, $hAddr );
      $bccAddr = array( $this->config->item( 'my_fromAddress' ) );

      $subject = "Lobball: Rainout reschedule for ".$oldGameDetails['Date'];

      $body  = "*** This is an automated message and a response is not required ***\n\n";
      $body .= "The game that was rained out\n\n";
      $body .= "Date: ".date( "D, j M Y", strtotime( $oldGameDetails['Date'] ) )."\n";
      $body .= "Time: ".$oldGameDetails['Time']."\n";
      $body .= "Diamond: ".$oldGameDetails['Diamond']."\n";
      $body .= $this->Model_Team->getTeamName( $oldGameDetails['VisitTeamID'] )." vs ".$this->Model_Team->getTeamName( $oldGameDetails['HomeTeamID'] )."\n\n";
      $body .= "has now been rescheduled for\n\n";
      $body .= "NEW Date: ".date( "D, j M Y", strtotime($newGameSlot['Date'] ) )."\n";
      $body .= "NEW Time: ".$newGameSlot['Time']."\n";
      $body .= "NEW Diamond: ".$newGameSlot['Diamond']."\n\n";
      $body .= "The schedule on the web has been updated appropriately. Please\n";
      $body .= "ensure your team members are appropriately notified.";

      $this->Model_Mail->sendTextEmail( $toAddr, array(), $bccAddr, $subject, $body );

/* ... inform the web user that the game has been shifted as directed */
?>
<script language="javascript">
   alert( "Changes to game have been saved in database. Emails sent to team contacts involved." );
   window.close();
</script>
<?php

/* ... time to go */
      $this->index();
      return;
    }



 } /* ... end of Controller */
