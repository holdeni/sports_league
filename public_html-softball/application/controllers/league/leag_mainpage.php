<?php

// $Id: leag_mainpage.php 84 2011-04-13 19:22:44Z Henry $
// Last Change: $Date: 2011-04-13 15:22:44 -0400 (Wed, 13 Apr 2011) $

class Leag_mainpage extends CI_Controller {

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

/* ... we can only proceed if the user is properly logged in and has appropriate role level */
      if (!$this->Model_Account->amILoggedIn()) {
         redirect( "mainpage/notLoggedIn", "refresh" );
       }
      if (!$this->Model_Account->hasAuthority( "COMMISH" )) {
         redirect( "mainpage/index", "refresh" );
       }

/* ... define values for template variables to display on page */
      if (!array_key_exists( "title", $data )) {
         $data['title'] = "League Admin - ".$this->config->item( 'siteName' );
       }

/* ... set the name of the page to be displayed */
      if (!array_key_exists( "main", $data )) {
         $data['main'] = "league/leag_home";
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
 * Function: registerTeam (Form to register new team in league)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function registerTeam() {

/* ... define values for template variables to display on page */
      $data['title'] = "New Team Registration - ".$this->config->item( 'siteName' );

/* ... set the name of the page to be displayed */
      $data['main'] = "league/leag_registerTeam";

/* ... complete our flow as we would for a normal page */      
      $this->index( $data );

/* ... time to go */
      return;
    }



/*****
 * Function: processRegistration (Used to check new team registration)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    
 *
 *****/
   function processRegistration() {

      $_SESSION['registerMsg'] = "unknown";
      
/* ... setup and perform validation on the basic data the new account form should have provided us */
      $this->load->library( 'form_validation' );
      $this->form_validation->set_rules( "teamname", "Team Name", "required|min_length[3]|max_length[128]" );
      $this->form_validation->set_rules( "division", "Division", "required|alpha" );
 
      if ($this->form_validation->run()) {

/* ... need to check to ensure we don't have an entry for this team name */
         if (!$this->Model_Team->checkTeam( $this->input->post( 'teamname' ) )) {

/* ... form was used correctly so now we see if we have a new team to add to our database */
            $data = array( 
               "TeamName"       => $this->input->post( 'teamname' ), 
               "Division"       => $this->input->post( 'division' ), 
               "CaptainID"      => -1,
               "CoCaptainID"    => -1,
               "ThirdContactID" => -1,
             );
            $this->Model_Team->addTeam( $data );
            $_SESSION['registerMsg'] = "valid";

          }
         else {
            $_SESSION['registerMsg'] = "invalid";
          }

       }
       
/* ... time to go - return to the registration form unless we've successfully saved a new account (which we are now logged in as) */
      if ($_SESSION['registerMsg'] == "valid" ) {
         $this->index();
       }
      else {
         $this->registerTeam();
       }
      return;
    }



/*****
 * Function: updateTeam (Form to update team registration details in league)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function updateTeam( $data = array() ) {

/* ... define values for template variables to display on page */
      $data['title'] = "Update Team Registration - ".$this->config->item( 'siteName' );
      $data['teamList'] = $this->Model_Team->getListOfTeams();
      if (!array_key_exists( "teamData", $data )) {
         $data['teamData'] = array(
            "CaptainID" => "",
            "CoCaptainID" => "",
            "ThirdContactID" => "",
          );
         $data['teamSelected'] = false;
       }
      else {

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
          
       }

/* ... set the name of the page to be displayed */
      $data['main'] = "league/leag_updateTeam";

/* ... complete our flow as we would for a normal page */      
      $this->index( $data );

/* ... time to go */
      return;
    }



/*****
 * Function: updateTeamDetails (Process a change to team information)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function updateTeamDetails() {

/* ... data declarations */
      $data['teamSelected'] = true;

/* ... are we coming through on our first pass, which means we just selected a team */
      if ($this->input->post( 'formpass' ) == 1) {
         
/* ... get the details for the team selected */
         $teamID = $this->input->post( 'teamname' );
         $data['teamData'] = $this->Model_Team->getTeamDetails( $teamID );

/* ... return to the basic form to show the full team details now */
         $this->updateTeam( $data );
       }
      else {

/* ... or is this our 2nd pass which means we have team data to review and save */
/* ... setup and perform validation on the basic data the new account form should have provided us */
         $_SESSION['updateMsg'] = "unknown";
         $this->load->library( 'form_validation' );
         $this->form_validation->set_rules( "division", "Division", "required|alpha" );
         $this->form_validation->set_rules( "captainid", "Captain Contact", "trim|required|valid_email" );
         $this->form_validation->set_rules( "cocaptainid", "CoCaptain Contact", "trim|required|valid_email" );
         $this->form_validation->set_rules( "thirdcontactid", "Third Contact", "trim|valid_email" );
 
 /* ... we need to determine if the contact emails are known to us as account holders or are addresses we are just */
 /*     going to keep with the team contact section */
         if ($this->Model_Account->checkEmail( $this->input->post( 'captainid' ) )) {
            $captainID = $this->Model_Account->getUserID( $this->input->post( 'captainid' ) );
            $captainEmail = NULL;
          }
         else {
            $captainID = -1;
            $captainEmail = $this->input->post( 'captainid' );
          }
          
         if ($this->Model_Account->checkEmail( $this->input->post( 'cocaptainid' ) )) {
            $coCaptainID = $this->Model_Account->getUserID( $this->input->post( 'cocaptainid' ) );
            $coCaptainEmail = NULL;
          }
         else {
            $coCaptainID = -1;
            $coCaptainEmail = $this->input->post( 'cocaptainid' );
          }
          
         if ($this->Model_Account->checkEmail( $this->input->post( 'thirdcontactid' ) )) {
            $thirdContactID = $this->Model_Account->getUserID( $this->input->post( 'thirdcontactid' ) );
            $thirdContactEmail = NULL;
          }
         else {
            $thirdContactID = -1;
            $thirdContactEmail = $this->input->post( 'thirdcontactid' );
          }
          
         $data['teamData'] = array( 
            "TeamID"         => $this->input->post( 'teamname' ),
            "TeamName"       => $this->Model_Team->getTeamName( $this->input->post( 'teamname' ) ),
            "Division"       => $this->input->post( 'division' ), 
            "CaptainID"      => $captainID,
            "CoCaptainID"    => $coCaptainID,
            "ThirdContactID" => $thirdContactID,
            "CaptainEmail"   => trim( $captainEmail ),
            "CoCaptainEmail" => trim( $coCaptainEmail ),
            "ThirdContactEmail" => trim( $thirdContactEmail ),
          );

         if ($this->form_validation->run()) {

/* ... form was used correctly so now we save updated team information */
            $this->Model_Team->updateTeam( $this->input->post( 'teamname' ), $data['teamData'] );
            $_SESSION['updateMsg'] = "valid";
          }

/* ... see if we are done with form or have form errors that we show to user */
         if ($_SESSION['updateMsg'] == "valid" ) {
            $_SESSION['statusMsg'] = "Team information successfully updated.";
            $this->updateTeam( $data );
          }
         else {
            $this->updateTeam( $data );
          }
       }

      
/* ... time to go */
      return;
    }

 } /* ... end of Class */