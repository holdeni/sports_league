<?php

// $Id: profile.php 84 2011-04-13 19:22:44Z Henry $
// Last Change: $Date: 2011-04-13 15:22:44 -0400 (Wed, 13 Apr 2011) $

class Profile extends CI_Controller {

/*****
 * Function: Profile (constructor)
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
   function index( $formData = "" ) {

/* ... define values for template variables to display on page */
      $data['title'] = "Profile - ".$this->config->item( 'siteName' );
      
      if (!is_array( $formData )) {
         $data['acctDetails'] = $this->Model_Account->getAccountDetails( $_SESSION['UserId'] );
         $data['acctDetails']['Password'] = NULL;
         $data['acctDetails']['PasswordConf'] = $data['acctDetails']['Password'];
       }
      else {
         $data['acctDetails'] = $formData;
       }

/* ... set the name of the page to be displayed */
      $data['main'] = "profile";

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
 * Function: processChanges (Used to check updated account registration)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    
 *
 *****/
   function processChanges() {

/* ... data declaration */
      $formComplete = false;
      $data = array( 
         "EmailAccount"     => $_SESSION['EmailAccount'], 
         "Password"  => $this->input->post( 'r_password' ), 
         "FirstName" => $this->input->post( 'firstname' ),
         "LastName"  => $this->input->post( 'lastname' ),
         "HomePhone" => $this->input->post( 'homephone' ),
         "WorkPhone" => $this->input->post( 'workphone' ),
         "CellPhone" => $this->input->post( 'cellphone' ),
         "AltEmail"  => $this->input->post( 'altemail' ),
         "Status"    => 'ACTIVE',
       );

/* ... setup and perform validation on the basic data the account form should have provided us */
      $this->load->library( 'form_validation' );
      $this->form_validation->set_rules( "r_password", "Password", "required|min_length[8]|callback_checkPasswordComplexity" );
      $this->form_validation->set_rules( "passwordConf", "Password Confirmation", "required|matches[r_password]" );
      $this->form_validation->set_rules( "firstname", "First Name", "required" );
      $this->form_validation->set_rules( "lastname", "Last Name", "required" );
      $this->form_validation->set_rules( "homephone", "Home Phone", "required|exact_length[10]|numeric" );
      $this->form_validation->set_rules( "workphone", "Work Phone", "exact_length[10]|numeric" );
      $this->form_validation->set_rules( "cellphone", "Cell Phone", "exact_length[10]|numeric" );
      $this->form_validation->set_rules( "altemail", "Alternative Email", "valid_email" );
 
      if ($this->form_validation->run()) {

/* ... time to encrypt the password for storage in the database */
         $data['Password'] = md5( $data['Password'] );
         
/* ... form was used correctly so now we update the record in our database */
         $this->Model_Account->updateAccount( $_SESSION['UserId'], $data );
         $this->Model_Account->loginAccount( $_SESSION['UserId'] );
         $formComplete = true;
         $_SESSION['statusMsg'] = "Account information successfully updated.";

/* ... send out an email to the account owner with the changes */
         $this->Model_Account->sendRegistrationEmail( $_SESSION['UserId'], $this->input->post( 'r_password' ), FALSE );


/* ... since we're going to re-display the profile information, we need to reset the password field */
         $data['Password'] = $this->input->post( 'r_password' );
         $data['PasswordConf'] = $data['Password'];
       }
      else {
         $data['PasswordConf'] = $this->input->post( 'passwordConf' );
       }


/* ... time to go back to the form */
      $this->index( $data );
      return;
    }



/*****
 * Function: checkPasswordComplexity (Check complexity of a password string against some basic rules)
 *
 * Arguments:
 *    $password - string containing password to be checked
 *
 * Returns:
 *    -none-
 *
 *****/
   function checkPasswordComplexity( $password ) {

      $this->load->library( "Passwordvalidator" );

/* ... check the complexity of the password */
      $this->passwordvalidator->setPassword( $password );
      if ($this->passwordvalidator->validate_non_numeric( 1 )) {                //Password must have 1 non-alpha character in it.
         $this->passwordvalidator->validate_whitespace();                       //No whitespace please
       }

/* ... if we didn't pass, set an appropriate error message */
      if ($this->passwordvalidator->getValid() == 0) {
         $this->form_validation->set_message('checkPasswordComplexity', 'The %s field must contain a string with no spaces and at least 1 non-alpha character');
       }

/* ... time to go */
      return( $this->passwordvalidator->getValid() == 1 ? TRUE : FALSE );
    }



 } /* ... end of Class */

