<?php

// $Id: leag_updateTeam.php 84 2011-04-13 19:22:44Z Henry $
// Last Change: $Date: 2011-04-13 15:22:44 -0400 (Wed, 13 Apr 2011) $

class Leag_updateTeam extends CI_Controller {

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
         $data['title'] = "Team Admin - ".$this->config->item( 'siteName' );
       }

/* ... get the details on the team for whom the user is a captain */
      $data['teamSelected'] = true;
      $data['teamData'] = $this->Model_Team->getTeamDetails( $_SESSION['TeamID'] );

/* ... need to figure out what email address we use for the contacts - are the contacts registered with us so */
/*     we use the ones with their registered accounts or do we have some freeform addresses just stored with the team details */
      if ($data['teamData']['CaptainID'] > 0) {
         $acctDetails = $this->Model_Account->getAccountDetails( $data['teamData']['CaptainID'] );
         $data['teamData']['CaptainEmail'] = $acctDetails['EmailAccount'];
       }

      if ($data['teamData']['CoCaptainID'] > 0) {
         $acctDetails = $this->Model_Account->getAccountDetails( $data['teamData']['CoCaptainID'] );
         $data['teamData']['CoCaptainEmail'] = $acctDetails['EmailAccount'];
       }

      if ($data['teamData']['ThirdContactID'] > 0) {
         $acctDetails = $this->Model_Account->getAccountDetails( $data['teamData']['ThirdContactID'] );
         $data['teamData']['ThirdContactEmail'] = $acctDetails['EmailAccount'];
       }

/* ... set the name of the page to be displayed */
      if (!array_key_exists( "main", $data )) {
         $data['main'] = "league/leag_updateTeam";
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



 } /* ... end of Controller */