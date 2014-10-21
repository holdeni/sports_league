<?php

// $Id: sched_tournament_load.php 195 2011-08-07 14:44:36Z Henry $
// Last Change: $Date: 2011-08-07 10:44:36 -0400 (Sun, 07 Aug 2011) $

class Sched_tournament_load extends CI_Controller {

/*****
 * Function: Sched_tournament_load (constructor)
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
   function index() {

/* ... if the user tries to get here without being logged in, kick'em to the curb! */
      if (!$this->Model_Account->amILoggedIn()) {
         redirect( "mainpage/notLoggedIn", "refresh" );
       }

/* ... check to ensure user has appropriate authority to run this process */
      if (!$this->Model_Account->hasAuthority( "ADMIN" )) {
         redirect( "mainpage/index", "refresh" );
       }

/* ... define values for template variables to display on page */
      $data['title'] = "Tournament Administration - ".$this->config->item( 'siteName' );

/* ... set the name of the page to be displayed */
      $data['main'] = "schedule/sched_tournament_load";

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
 * Function: processConfirmation (See if bulk load of tournament should or should not proceed)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function processConfirmation() {

/* ... pretty simple - if the user selected "YES", then do the bulk load; otherwise get the heck out of Dodge! */
      if ($this->input->post( 'confirm' ) == "YES") {
         $this->_loadTournament();
       }
      else {
         redirect( "league/leag_mainpage/index", "refresh" );
       }

/* ... time to go */
      return;
    }



/*****
 * Function: _loadTournament
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function _loadTournament() {

/* ... data declarations */
      $dbData = array();

/* ... if the user tries to get here without being logged in, kick'em to the curb! */
      if (!$this->Model_Account->amILoggedIn()) {
         redirect( "mainpage/notLoggedIn", "refresh" );
       }

/* ... check to ensure user has appropriate authority to run this process */
      if (!$this->Model_Account->hasAuthority( "ADMIN" )) {
         redirect( "mainpage/index", "refresh" );
       }

/* ... load a module to make progress messages easy to display */
      $this->load->helper('typography');
      print auto_typography( "Preparing for loading of tournament into database\n" );

/* ... ensure the data file is where we expect it to be */
      $csvFH = fopen( base_url().$this->config->item( 'bulkTournament' ), "r" );
      if (!$csvFH) {
         print auto_typography( "*** ERROR: error opening file\n" );
       }
      else {
         print auto_typography( "... ready to proceed processing data\n" );

/* ... delete the current table contents */
         print auto_typography( "... deleting current tournament details\n" );
         $this->db->empty_table( "Tournament" );

         $row = 1;
         do {

/* ... get the next schedule entry from the CSV file, skipping over the header line */
            $entryData = fgetcsv( $csvFH );
            if ($row != 1) {

/* ... take the date for the current entry and manipulate into the proper form for the database field */
//               $entryData[0] = $this->config->item( "thisYear" )."-".date( "m-d", strtotime( trim( $entryData[0] ) ) );

/* ... take the game time and remove the time suffix */
//               $tokens = explode( " ", trim( $entryData[1] ) );
//               $entryData[1] = $tokens[0];

/* ... get the team IDs if there are teams playing in this game slot */
//               $visitTeamID = -1;
//               $homeTeamID = -1;
//               if ($entryData[4] != "") {
//                  $visitTeamID = $this->Model_Team->getTeamID( trim( $entryData[4] ) );
//                  $homeTeamID = $this->Model_Team->getTeamID( trim( $entryData[5] ) );
//                  if ($visitTeamID == -1) {
//                     print auto_typography( "**** ERROR: Unable to get team ID for ".$entryData[4]." [".$entryData[0]." @ ".$entryData[1]." on ".$entryData[2]."]\n" );
//                   }
//                  if ($homeTeamID == -1) {
//                     print auto_typography( "**** ERROR: Unable to get team ID for ".$entryData[4]." [".$entryData[0]." @ ".$entryData[1]." on ".$entryData[2]."]\n" );
//                   }
//                }

/* ... build the data array that we will feed to the database insertion function */
               $dbData = array(
                  'Date' => $entryData[0],
                  'Time' => $entryData[1],
                  'Diamond' => trim( $entryData[2] ),
                  'HomeSeed' => $entryData[3] != "" ? $entryData[3] : NULL,
                  'VisitSeed' => $entryData[4] != "" ? $entryData[4] : NULL,
                  'WinnerNextGame' => $entryData[5],
                  'LoserNextGame' => $entryData[6],
                  'TournamentID' => $entryData[7]
                );
               if ($entryData[7] != "") {
                 $dbData = array_merge( $dbData, array(
                    'Status' => 'SCHEDULED',
                 ) );
                }
               print_r($dbData);
//               if ($visitTeamID != -1  &&  $homeTeamID != -1) {
//                  $dbData = array_merge( $dbData, array(
//                     'HomeTeamID' => $homeTeamID,
//                     'VisitTeamID' => $visitTeamID,
//                     'Status' => "SCHEDULED",
//                   ) );
//                }
               $this->db->insert( "Tournament", $dbData );

/* ... periodically we need to inform the user about our progress */
               if ($row % 50 == 0) {
                  print auto_typography( "      - loaded ".$row." games\n" );
                }
             }
            $row++;
          }
         while (!feof( $csvFH ));
         print auto_typography( "      - completed loading ".$row." games\n" );

/* ... close our data file as we are done */
         fclose( $csvFH );
         print auto_typography( "... tournament read in from data file\n" );

       }

/* ... time to go */
      echo form_open( "league/leag_mainpage/index" );
      echo "\n";
      echo form_submit( "continue", "Press to continue" );
      echo "\n";
      echo form_close();
      return;
    }



 } /* ... end of Class */
