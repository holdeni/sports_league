<?php
// $Id: model_account.php 84 2011-04-13 19:22:44Z Henry $
// Last Change: $Date: 2011-04-13 15:22:44 -0400 (Wed, 13 Apr 2011) $

class Model_Account extends CI_Model {

/*****
 * Function: Model_Account (constructor)
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
    }



/*****
 * Function: verifyAccount (Verify if we have correct password for a given password
 *
 * Arguments:
 *    $emailAddr - email address associated with acount (meant to be unique per account)
 *    $password - password for associated account
 *
 * Returns:
 *    numeric account ID or -1 if account/pw incorrect or do not correspond
 *
 *****/
   function verifyAccount( $emailAddr, $password ) {

/* ... form the database query */
      $this->db->select( 'UserId' );
      $this->db->where( 'EmailAccount', $emailAddr );
      $this->db->where( 'Password', md5( $password ) );
      $this->db->where( 'Status', "ACTIVE" );
      $this->db->limit( 1 );

/* ... see if account matching address and password exists */
      $sqlQuery = $this->db->get( 'Users' );

/* ... if it exists, update the PHP session values we're caching some user information in, otherwise prepare
       an error message for user */
      if ($sqlQuery->num_rows() > 0) {
         $dbRow = $sqlQuery->row_array();
         $userId = $dbRow['UserId'];
       }
      else {
         
         $userId = -1;
       }

/* ... time to go */
      return( $userId );
    }



/*****
 * Function: getAccountDetails
 *
 * Arguments:
 *    $userId - numeric ID associated with each account (meant to be unique per account)
 *
 * Returns:
 *    associative array with field data from Users table (minus Password string)
 *
 *****/
   function getAccountDetails( $userId ) {

/* ... data declarations */
      $dbRow = array();

/* ... form the database query */
      $this->db->select( 'EmailAccount, Status, FirstName, LastName, HomePhone, CellPhone, WorkPhone, AltEmail' );
      $this->db->where ('UserId', $userId );
      $this->db->limit( 1 );

/* ... issue the query and if data found, put it in the return array */
      $sqlQuery = $this->db->get( 'Users' );
      if ($sqlQuery->num_rows() > 0) {
         $dbRow = $sqlQuery->row_array();
       }

/* ... time to go */
      return( $dbRow );
    }



/*****
 * Function: getAccountPassword
 *
 * Arguments:
 *    $userId - numeric ID associated with each account (meant to be unique per account)
 *
 * Returns:
 *    string containing account's password
 *
 *****/
   function getAccountPassword( $userId ) {

/* ... data declarations */
      $dbRow = array();

/* ... form the database query */
      $this->db->select( 'Password' );
      $this->db->where ('UserId', $userId );
      $this->db->limit( 1 );

/* ... issue the query and if data found, put it in the return array */
      $sqlQuery = $this->db->get( 'Users' );
      if ($sqlQuery->num_rows() > 0) {
         $dbRow = $sqlQuery->row_array();
       }

/* ... time to go */
      return( $dbRow['Password'] );
    }



/*****
 * Function: addAccount
 *
 * Arguments:
 *    $data -  associative array with data for fields for a new user record
 *
 * Returns:
 *    numeric ID associated with account (> 0) or -1 otherwise
 *
 *****/
   function addAccount( $data ) {

/* ... add the new record to the database */
      $this->db->insert( 'Users', $data );
      
/* ... get the unique numeric ID that was associated with the record */
      $userId = $this->db->insert_id();
      $userId = $userId > 0 ? $userId : -1;

/* ... time  to go */
      return( $userId );
    }



/*****
 * Function: updateAccount
 *
 * Arguments:
 *    $userId - numeric ID of account to be updated
 *    $data -  associative array with data for fields for a existing user record
 *
 * Returns:
 *    -none-
 *
 *****/
   function updateAccount( $userId, $data ) {

/* ... update the record in the database */
      $this->db->where( 'UserId', $userId );
      $this->db->update( 'Users', $data );
      
/* ... time  to go */
      return;
    }



/*****
 * Function: checkEmail (See if we have record for given email address)
 *
 * Arguments:
 *    $emailAddr - email address to be checked
 *
 * Returns:
 *    boolean value indicating match or not for given email address
 *
 *****/
   function checkEmail( $emailAddr ) {

/* ... form the database query */
      $this->db->select( 'UserId' );
      $this->db->where( 'EmailAccount', $emailAddr );
      $this->db->limit( 1 );

/* ... see if account matching address exists */
      $sqlQuery = $this->db->get( 'Users' );
      $retFlag = $sqlQuery->num_rows() > 0 ? true : false;

/* ... time to go */
      return( $retFlag );
    }



/*****
 * Function: loginAccount (Login to an account by accessing key data and storing it in SESSION variables)
 *
 * Arguments:
 *    $userId - numeric identifier for a given known account
 *
 * Returns:
 *    -none-
 *
 *****/
   function loginAccount( $userId ) {

/* ... form the database query */
      $this->db->select( 'EmailAccount, FirstName, LastName' );
      $this->db->where( 'UserId', $userId );
      $this->db->where( 'Status', "ACTIVE" );
      $this->db->limit( 1 );

/* ... see if account matching address and password exists */
      $sqlQuery = $this->db->get( 'Users' );

/* ... if it exists, update the PHP session values we're caching some user information in, otherwise prepare
       an error message for user */
      if ($sqlQuery->num_rows() > 0) {
         $dbRow = $sqlQuery->row_array();
         $_SESSION['UserId'] = $userId;
         $_SESSION['EmailAccount'] = $dbRow['EmailAccount'];
         $_SESSION['FirstName'] = $dbRow['FirstName'];
         $_SESSION['LastName'] = $dbRow['LastName'];
         $_SESSION['freshLogin'] = true;
         $teamID = $this->Model_Team->areWeACaptain( $userId );
         if ($teamID != -1) {
            $_SESSION['TeamID'] = $teamID;
          }
       }
      else {
         $this->logoutAccount();
       }

/* ... time to go */
      return;
    }



/*****
 * Function: logout (Used to clear SESSION variables associated with a logged in account)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function logoutAccount() {

/* ... remove all appropriate SESSION variables */
      $sessVars = array( 'UserId', 'EmailAccount', 'FirstName', 'LastName', 'registerMsg', 'TeamID' );
      foreach ($sessVars as $index) {
         if (array_key_exists( $index, $_SESSION )) {
            unset( $_SESSION[$index] );
          }
       }
      unset( $_SESSION['loginMsg'] );

/* ... time to go */
      return;
    }



/*****
 * Function: getAccountRole
 *
 * Arguments:
 *    $userId - numeric ID associated with each account (meant to be unique per account)
 *
 * Returns:
 *    string containing account's role
 *
 *****/
   function getAccountRole( $userId ) {

/* ... data declarations */
      $dbRow = array();
      $role = "";

/* ... form the database query */
      $this->db->select( 'Role' );
      $this->db->where ('UserId', $userId );
      $this->db->limit( 1 );

/* ... issue the query and if data found, put it in the return array */
      $sqlQuery = $this->db->get( 'Roles' );
      if ($sqlQuery->num_rows() > 0) {
         $dbRow = $sqlQuery->row_array();
         $role = $dbRow['Role'];
       }

/* ... if we don't have a special high level role, see if we are a captain */
      if ($role == "") {
         if (array_key_exists( "TeamID", $_SESSION )) {
            $role = "CAPTAIN";
          }
       }

/* ... time to go */
      return( $role );
    }



/*****
 * Function: hasAuthority (Does user's role meet certain level)
 *
 * Arguments:
 *    $reqdRole - required role level
 *
 * Returns:
 *    boolean value TRUE if user's role meets or exceeds desired role level, false otherwise
 *
 *****/
   function hasAuthority( $reqdRole ) {

/* ... data declarations */
      $roleCheck = false;
      
/* ... get the role level for the current user */
      $currRole = $this->getAccountRole( $_SESSION['UserId'] );
      if ($currRole == ""  &&  $reqdRole == "FAN") {
         $roleCheck = true;
       }
      elseif ($currRole == "PLAYER"  &&  ($reqdRole == "FAN"  ||  $reqdRole == "PLAYER")) {
         $roleCheck = true;
       }
      elseif ($currRole == "CAPTAIN"  &&  ($reqdRole == "FAN"  ||  $reqdRole == "PLAYER"  ||  $reqdRole == "CAPTAIN")) {
         $roleCheck = true;
       }
      elseif ($currRole == "COMMISH"  &&
             ($reqdRole == "FAN"  ||  $reqdRole == "PLAYER"  || $reqdRole == "CAPTAIN"  || $reqdRole == "COMMISH")) {
         $roleCheck = true;
       }
      elseif ($currRole == "ADMIN") {
         $roleCheck = true;
       }
      
/* ... time to go */
      return( $roleCheck );
    }



/*****
 * Function: getUserID
 *
 * Arguments:
 *    $emailAddr - email address associated with each account
 *
 * Returns:
 *    numeric account ID
 *
 *****/
   function getUserID( $emailAddr ) {

/* ... data declarations */
      $dbRow = array();
      $userID = "";

/* ... form the database query */
      $this->db->select( 'UserId' );
      $this->db->where ('EmailAccount', $emailAddr );
      $this->db->limit( 1 );

/* ... issue the query and if data found, put it in the return array */
      $sqlQuery = $this->db->get( 'Users' );
      if ($sqlQuery->num_rows() > 0) {
         $dbRow = $sqlQuery->row_array();
         $userID = $dbRow['UserId'];
       }

/* ... time to go */
      return( $userID );
    }



/*****
 * Function: getAccountEmailAddr (Get all email addresses associated with an account)
 *
 * Arguments:
 *    $userID - numeric identifier for account
 *
 * Returns:
 *    array containing email addresses
 *
 *****/
   function getAccountEmailAddr( $userID ) {

/* ... data declarations */
      $emailAddr = array();
      
/* ... get the account details and grab the primary email address */
      $contactDetails = $this->getAccountDetails( $userID );
      $emailAddr[] = $contactDetails['EmailAccount'];

/* ... if the account has an alternate email address listed, we need to use it also */
      if ($contactDetails['AltEmail'] != "") {
         $emailAddr[] = $contactDetails['AltEmail'];
       }

/* ... time to go */
      return( $emailAddr );
    }



/*****
 * Function: getName (Get the first and last name for an account)
 *
 * Arguments:
 *    $userId - numeric ID associated with each account (meant to be unique per account)
 *
 * Returns:
 *    string with first name and last name concatenanted
 *
 *****/
   function getName( $userId ) {

/* ... data declarations */
      $dbRow = array();
      $name = NULL;

/* ... form the database query */
      $this->db->select( 'FirstName, LastName' );
      $this->db->where ('UserId', $userId );
      $this->db->limit( 1 );

/* ... issue the query and if data found, put it in the return array */
      $sqlQuery = $this->db->get( 'Users' );
      if ($sqlQuery->num_rows() > 0) {
         $dbRow = $sqlQuery->row_array();
         $name = $dbRow['FirstName']." ".$dbRow['LastName'];
       }

/* ... time to go */
      return( $name );
    }



/*****
 * Function: amILoggedIn (Check to see if user has completed login process)
 *
 * Arguments:
 *    -none
 *
 * Returns:
 *    TRUE if logged in, FALSE otherwise
 *
 *****/
   function amILoggedIn() {

/* ... data declarations */
      $checkFlag = FALSE;

/* ... see if expected session variables are in play */
      if (array_key_exists( "UserId", $_SESSION )) {
         $checkFlag = TRUE;
       }

/* ... time to go */
      return( $checkFlag );
    }



/*****
 * Function: sendRegistrationEmail (Send an email to an account owner upon registration)
 *
 * Arguments:
 *    $userID - Numeric user identification
 *    $password - cleartext account password
 *    $newAcct - boolean of TRUE if new account, FALSE otherwise
 *
 * Returns:
 *    -none-
 *
 *****/
   function sendRegistrationEmail( $userID, $password, $newAcct ) {

/* ... get the information on the account */
      $userDetails = $this->getAccountDetails( $userID );
      
/* ... create the email to be sent */
      $toAddr = array( $userDetails['EmailAccount'] );
      
      if ($newAcct) {
         $subject = "New account created at Kanata Lobball website";
       }
      else {
         $subject = "Account updated at Kanata Lobball website";
       }
       
      $body  = "This email has been sent by an automated process and does not require a reply.\n\n";
      
      if ($newAcct) {
         $body .= "A new account has been registered with this email address at the Kanata Mens Lobball website ";
         $body .= "(".base_url()."). The new account details are: \n";
       }
      else {
         $body .= "Your account has recently been updated at the Kanata Mens Lobball website ";
         $body .= "(".base_url()."). The updated account details are:\n";
       }
       
      $body .= "\n";
      $body .= "Name: ".$userDetails['FirstName']." ".$userDetails['LastName']."\n";
      $body .= "Password: ".$password."\n";
      $body .= "Home Phone: ".formatPhoneNr( $userDetails['HomePhone'] )."\n";
      $body .= "Work Phone: ".formatPhoneNr( $userDetails['WorkPhone'] )."\n";
      $body .= "Cell Phone: ".formatPhoneNr( $userDetails['CellPhone'] )."\n";
      $body .= "Alternate email address: ".$userDetails['AltEmail']."\n";
      $body .= "\n";
      $body .= "If any information above is incorrect, you may change it by logging into this account on the website ";
      $body .= "and choosing 'my account' from the left hand contextual menu.\n";
      $body .= "\n";
      
      if ($newAcct) {
         $body .= "If this account has been created in error, please forward this email to ".$this->config->item( 'my_replyAddress' )." and ";
         $body .= "just indicate you wish the account to be removed.\n";
       }
      else {
         $body .= "If you did not recently update this account, please forward this email to ".$this->config->item( 'my_replyAddress' )." so ";
         $body .= "that we may investigate.\n";
       }

/* ... send the email out */
      $this->Model_Mail->sendTextEmail( $toAddr, array(), array(), $subject, $body );

/* ... time to go */
      return;
    }



 } /* ... end of Class */
