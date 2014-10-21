<?php

// $Id: sched_mainpage.php 218 2012-03-26 22:44:20Z Henry $
// Last Change: $Date: 2012-03-26 18:44:20 -0400 (Mon, 26 Mar 2012) $

class Sched_mainpage extends CI_Controller {

/*****
 * Function: Sched_mainpage (constructor)
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

/* ... define values for template variables to display on page */
      if (!array_key_exists( "title", $data )) {
         $data['title'] = "Schedules - ".$this->config->item( 'siteName' );
       }

/* ... set the name of the page to be displayed */
      if (!array_key_exists( "main", $data )) {
         $data['main'] = "schedule/sched_home";
       }

/* ... build dataset for form that lists league teams */
      $teamList = $this->Model_Team->getListOfTeams();
      $data['formFields'] = array();
      $data['formFields'][] = array( 
         'fieldName' => "teamname",
         'fieldText' => "Team",
         'required' => false,
         'type' => "dropdown",
         'default' => " ",
       );
      $data['options']['teamname'] = array( 
         ' ' => " ",
       );
      foreach ($teamList as $teamID => $teamName) {
         $data['options']['teamname'][$teamID] = htmlspecialchars( $teamName );
       }

      $data['data']['teamname'] = array(
         'id' => "teamname",
       );

/* ... build dataset for form that lists divisions */
      $divList = $this->Model_Team->getListOfDivisions();
      $data['formFields2'] = array();
      $data['formFields2'][] = array( 
         'fieldName' => "division",
         'fieldText' => "Division",
         'required' => false,
         'type' => "dropdown",
         'default' => " ",
       );
      $data['options2']['division'] = array( 
         ' ' => " ",
       );
      foreach ($divList as $div) {
         $data['options2']['division'][$div] = htmlspecialchars( $div );
       }

      $data['data2']['division'] = array(
         'id' => "division",
       );

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
 * Function: showMaster (Show master schedule)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function showMaster() {

/* ... define values for template variables to display on page */
      $data['title'] = "Master Schedule - ".$this->config->item( 'siteName' );

/* ... get the schedule details from the database */      
      $data['schedDetails'] = $this->Model_Schedule->getScheduleDetails();
      $data['scheduleHeader'] = "League Master Schedule";
         $data['scheduleFormat'] = array(
            'type' => 'league',
          );


/* ... set the name of the page to be displayed */
      $data['main'] = "schedule/sched_master";

      $this->index( $data );

/* ... time to go */
      return;
    }



/*****
 * Function: showTeam (Show schedule for a specific team)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function showTeam() {

/* ... define values for template variables to display on page */
      $data['title'] = "Team Schedule - ".$this->config->item( 'siteName' );

/* ... get the schedule details from the database */
      $data['schedDetails'] = array();
      if ($this->input->post( 'teamname' ) != " ") {
         $data['schedDetails'] = $this->Model_Schedule->getScheduleDetails( $this->input->post( 'teamname' ) );
         $data['scheduleHeader'] = "Team Schedule For ".htmlspecialchars( $this->Model_Team->getTeamName( $this->input->post( 'teamname' ) ) );
         $data['scheduleFormat'] = array(
            'type' => 'team',
          );
         $data['schedTeamID'] = $this->input->post( 'teamname' );
       }
      else{
         $this->index();
         return;
       }

/* ... time to prepare for our alternative format of the schedule */
      $this->load->library( 'calendar', $this->_setCalendarFormat() );

/* ... initialize our structure to keep track of which months we have games scheduled in */
      for ($i = 1; $i <= 12; $i++) {
         $monthUsed[$i] = false;
       }
    

/* ... we need to break the data out by month and day of month in order to present information on calendar */
      for ($i = 0; $i < count( $data['schedDetails'] ); $i++) {
         $gameDate = explode( "-", $data['schedDetails'][$i]['Date'] );
         $gmMonth = (int) $gameDate[1];           // We need to convert from a string type to an integer type,
         $gmDay = (int) $gameDate[2];             // so we use a type cast;
         $schedule[$gmMonth][$gmDay][] = $i;
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

/* ... start with the time and diamond information */
                  $dateInfo = $data['schedDetails'][$index]['Time']." @ ".$data['schedDetails'][$index]['Diamond'];

/* ... if it's an unplayed game, show who the opponent will be */
                  if ($data['schedDetails'][$index]['Status'] == "SCHEDULED") {
                     if ($data['schedTeamID'] == $data['schedDetails'][$index]['HomeTeamID']) {
                        $dateInfo .= " vs ".htmlspecialchars( $this->Model_Team->getTeamName( $data['schedDetails'][$index]['VisitTeamID'] ) );
                      }
                     else {
                        $dateInfo .= " at ".htmlspecialchars( $this->Model_Team->getTeamName( $data['schedDetails'][$index]['HomeTeamID'] ) );
                      }
                   }

/* ... if it was a rained out game that hasn't been rescheduled, show who the opponent was */
                  elseif ($data['schedDetails'][$index]['Status'] == "RAINOUT") {
                     if ($data['schedTeamID'] == $data['schedDetails'][$index]['HomeTeamID']) {
                        $dateInfo .= " RAINOUT vs ".htmlspecialchars( $this->Model_Team->getTeamName( $data['schedDetails'][$index]['VisitTeamID'] ) );
                      }
                     else {
                        $dateInfo .= " RAINOUT at ".htmlspecialchars( $this->Model_Team->getTeamName( $data['schedDetails'][$index]['HomeTeamID'] ) );
                      }
                   }

/* ... otherwise it was a game that was played, so show the score of the game */
                  else {
                     $gameResult = $this->Model_Schedule->gameResult( $data['schedDetails'][$index]['GameID'] );
                     if ($data['schedTeamID'] == $data['schedDetails'][$index]['HomeTeamID']) {
                        if ($gameResult['Result'] == "HOME") {
                           $dateInfo = "W (".$data['schedDetails'][$index]['HomeScore']."-".$data['schedDetails'][$index]['VisitScore'].") ";
                           $dateInfo .= "vs ".htmlspecialchars( $this->Model_Team->getTeamName( $data['schedDetails'][$index]['VisitTeamID'] ) );
                         }
                        elseif ($gameResult['Result'] == "VISIT") {
                           $dateInfo = "L (".$data['schedDetails'][$index]['HomeScore']."-".$data['schedDetails'][$index]['VisitScore'].") ";
                           $dateInfo .= "vs ".htmlspecialchars( $this->Model_Team->getTeamName( $data['schedDetails'][$index]['VisitTeamID'] ) );
                         }
                        else {
                           $dateInfo = "T (".$data['schedDetails'][$index]['HomeScore']."-".$data['schedDetails'][$index]['VisitScore'].") ";
                           $dateInfo .= "vs ".htmlspecialchars( $this->Model_Team->getTeamName( $data['schedDetails'][$index]['VisitTeamID'] ) );
                         }
                      }
                     else {
                        if ($gameResult['Result'] == "VISIT") {
                           $dateInfo = "W (".$data['schedDetails'][$index]['VisitScore']."-".$data['schedDetails'][$index]['HomeScore'].") ";
                           $dateInfo .= "vs ".htmlspecialchars( $this->Model_Team->getTeamName( $data['schedDetails'][$index]['HomeTeamID'] ) );
                         }
                        elseif ($gameResult['Result'] == "HOME") {
                           $dateInfo = "L (".$data['schedDetails'][$index]['VisitScore']."-".$data['schedDetails'][$index]['HomeScore'].") ";
                           $dateInfo .= "vs ".htmlspecialchars( $this->Model_Team->getTeamName( $data['schedDetails'][$index]['HomeTeamID'] ) );
                         }
                        else {
                           $dateInfo = "T (".$data['schedDetails'][$index]['HomeScore']."-".$data['schedDetails'][$index]['VisitScore'].") ";
                           $dateInfo .= "vs ".htmlspecialchars( $this->Model_Team->getTeamName( $data['schedDetails'][$index]['HomeTeamID'] ) );
                         }
                      }
                   }

/* ... save this game's detail after seeing if this is the first game for the day or do we add it to an already existing game entry */
                  if (!isset( $monthData[$day] )) {
                     $monthData[$day] = $dateInfo;
                   }
                  else {
                     $monthData[$day] .= "<br /><br />".$dateInfo;
                   }
                }
             }

/* ... make the month's calendar */
            $data['calendarSchedule'][$i] = $this->calendar->generate( $this->config->item( 'thisYear' ), $i, $monthData );

          }
       }

/* ... set the name of the page to be displayed */
      $data['main'] = "schedule/sched_master";

      $this->index( $data );

/* ... time to go */
      return;
    }



/*****
 * Function: showDiv (Show schedule for a division)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function showDivision() {

/* ... define values for template variables to display on page */
      $data['title'] = "Division Schedule - ".$this->config->item( 'siteName' );

/* ... get the schedule details from the database */
      $data['schedDetails'] = array();
      if ($this->input->post( 'division' ) != " ") {
         $data['schedDetails'] = $this->Model_Schedule->getScheduleDetails( $this->input->post( 'division' ) );
         $data['scheduleHeader'] = "Division ".$this->input->post( 'division' )." Schedule";
         $data['scheduleFormat'] = array(
            'type' => 'division',
          );
       }
      else{
         $this->index();
         return;
       }

/* ... set the name of the page to be displayed */
      $data['main'] = "schedule/sched_master";

      $this->index( $data );

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
 * Function: emailSchedule (Email schedule to current web user)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function emailSchedule() {

/* ... this form is not to be shown when not logged in; show login required page */
   if (!$this->Model_Account->amILoggedIn()) {
      redirect( "mainpage/notLoggedIn", "refresh" );;
    }

/* ... build the email message for the recently viewed schedule */
      $toAddr = array( $_SESSION['EmailAccount'] );
      $ccAddr = array();
      $bccAddr = array();
      $subject = $this->config->item( 'siteName' )." - ".htmlspecialchars_decode( $_SESSION['scheduleHeader'] );
      $body  = "<p>This email has been sent by an automated process and does not require a reply.</p>";
      $body .= $_SESSION['schedulePage'];

      $this->Model_Mail->sendHTMLEmail( $toAddr, $ccAddr, $bccAddr, $subject, $body, "scheduleMail.css" );
      
/* ... a web trick that will popup a window for the user telling them their information was accepted */
?>
<script language="javascript">
   alert( "Copy of schedule has been emailed to your registered email address." );
   window.close();
</script>
<?php

/* ... time to go */
      unset( $_SESSION['schedulePage'] );
      unset( $_SESSION['scheduleHeader'] );
      $this->index();
      return;
    }



/*****
 * Function: showOpenSlots (Show all time slots that have no game scheduled)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function showOpenSlots() {

/* ... define values for template variables to display on page */
      $data['title'] = "Open Slots - ".$this->config->item( 'siteName' );

/* ... get the schedule details from the database */      
      $data['schedDetails'] = $this->Model_Schedule->getOpenSlots();
      $data['scheduleHeader'] = "League's Open Slots";
         $data['scheduleFormat'] = array(
            'type' => 'league',
          );


/* ... set the name of the page to be displayed */
      $data['main'] = "schedule/sched_openSlots";

      $this->index( $data );

/* ... time to go */
      return;
    }



 } /* ... end of Class */