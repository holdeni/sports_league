<?php

// $Id: contacts.php 223 2012-03-26 23:26:44Z Henry $
// Last Change: $Date: 2012-03-26 19:26:44 -0400 (Mon, 26 Mar 2012) $

class Contacts extends CI_Controller {

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
      session_start();        // Enable if PHP sessions are necessary to work
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
      if (!array_key_exists( 'title', $data )) {
         $data['title'] = "Contacts - ".$this->config->item( 'siteName' );
       }

/* ... set the name of the page to be displayed */
      if (!array_key_exists( 'main', $data )) {
         $data['main'] = "contacts";
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

/* ... set up the jQuery scripts we need in order to nicely format this page */
      $data['pageJavaScript'] = "
         $(document).ready( function() { 
            $('.personRow:odd').addClass( 'odd' );
            $('.personRow:even').addClass( 'even' );
          } );
         \n";

/* ... enable our template variables and then display the template, as we want it shown */      
      $this->load->vars( $data );
      $this->load->view( "template" );


/* ... time to go */
      return;
    }



/*****
 * Function: showLeague (Show all contacts for all teams)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function showLeague() {

/* ... we can only proceed if the user is properly logged in */
      if (!$this->Model_Account->amILoggedIn()) {
         redirect( "mainpage/notLoggedIn", "refresh" );
       }

/* ... define values for template variables to display on page */
      $data['title'] = "Team Contacts - ".$this->config->item( 'siteName' );

/* ... get the contact details for all teams in the division (if one was chosen by the form) */
      $data['contactDetails'] = array();
      $data['listOfTeams'] = array();
      
      $leagueTeams = $this->Model_Team->getListOfTeams();
      foreach ($leagueTeams as $teamID => $teamName) {
         $data['listOfTeams'][] = $teamID;
         $data['contactDetails'][] = $this->Model_Team->buildTeamContacts( $teamID );
       }
      $data['header'] = "League Contacts";
      $data['contactFormat'] = array(
         'type' => 'league',
       );

/* ... set up the jQuery scripts we need in order to nicely format this page */
      $data['pageJavaScript'] = "
         $(document).ready( function() { 
            $('.contactRow:odd').addClass( 'odd' );
            $('.contactRow:even').addClass( 'even' );
          } );
         \n";

/* ... set the name of the page to be displayed */
      $data['main'] = "contactDisplay";

      $this->index( $data );

/* ... time to go */
      return;
    }



/*****
 * Function: showTeam (Show the contact details for s specific team)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function showTeam() {

/* ... we can only proceed if the user is properly logged in */
      if (!$this->Model_Account->amILoggedIn()) {
         redirect( "mainpage/notLoggedIn", "refresh" );
       }

/* ... define values for template variables to display on page */
      $data['title'] = "Team Contacts - ".$this->config->item( 'siteName' );

/* ... get the contact details for a specified team from the database (if one was chosen by the form) */
      $data['contactDetails'] = array();
      if ($this->input->post( 'teamname' ) != " ") {
         $data['contactDetails'][0] = $this->Model_Team->buildTeamContacts( $this->input->post( 'teamname' ) );
         $data['header'] = "Contacts For ".htmlspecialchars( $this->Model_Team->getTeamName( $this->input->post( 'teamname' ) ) );
         $data['contactFormat'] = array(
            'type' => 'team',
          );
         $data['listOfTeams'][0] = $this->input->post( 'teamname' );
       }
      else{
         $this->index();
         return;
       }

/* ... set the name of the page to be displayed */
      $data['main'] = "contactDisplay";

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

/* ... we can only proceed if the user is properly logged in */
      if (!$this->Model_Account->amILoggedIn()) {
         redirect( "mainpage/notLoggedIn", "refresh" );
       }

/* ... define values for template variables to display on page */
      $data['title'] = "Division Contacts - ".$this->config->item( 'siteName' );

/* ... get the contact details for all teams in the division (if one was chosen by the form) */
      $data['contactDetails'] = array();
      if ($this->input->post( 'division' ) != " ") {
         $data['listOfTeams'] = $this->Model_Team->getTeamsInDivision( $this->input->post( 'division' ), TRUE );
         foreach ($data['listOfTeams'] as $teamID) {
            $data['contactDetails'][] = $this->Model_Team->buildTeamContacts( $teamID );
          }
         $data['header'] = "Division ".$this->input->post( 'division' )." Contacts";
         $data['contactFormat'] = array(
            'type' => 'division',
          );
       }
      else{
         $this->index();
         return;
       }

/* ... set up the jQuery scripts we need in order to nicely format this page */
      $data['pageJavaScript'] = "
         $(document).ready( function() { 
            $('.contactRow:odd').addClass( 'odd' );
            $('.contactRow:even').addClass( 'even' );
          } );
         \n";

/* ... set the name of the page to be displayed */
      $data['main'] = "contactDisplay";

      $this->index( $data );

/* ... time to go */
      return;
    }



 } /* ... end of Controller */
 
