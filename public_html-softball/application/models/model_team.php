<?php
// $Id: model_team.php 241 2012-05-01 00:24:45Z Henry $
// Last Change: $Date: 2012-04-30 20:24:45 -0400 (Mon, 30 Apr 2012) $

class Model_Team extends CI_Model {

/*****
 * Function: Model_Team (constructor)
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
 * Function: getTeamDetails
 *
 * Arguments:
 *    $teamID - numeric ID associated with each team (meant to be unique per team)
 *
 * Returns:
 *    associative array with field data from Teams table
 *
 *****/
   function getTeamDetails( $teamID ) {

/* ... data declarations */
      $dbRow = array();

/* ... form the database query */
      $this->db->select( 'TeamID, TeamName, Division, CaptainID, CoCaptainID, ThirdContactID, CaptainEmail, CoCaptainEmail, ThirdContactEmail' );
      $this->db->where ('TeamID', $teamID );
      $this->db->limit( 1 );

/* ... issue the query and if data found, put it in the return array */
      $sqlQuery = $this->db->get( 'Teams' );
      if ($sqlQuery->num_rows() > 0) {
         $dbRow = $sqlQuery->row_array();
       }

/* ... time to go */
      return( $dbRow );
    }



/*****
 * Function: addTeam
 *
 * Arguments:
 *    $data -  associative array with data for fields for a new team record
 *
 * Returns:
 *    numeric ID associated with account (> 0) or -1 otherwise
 *
 *****/
   function addTeam( $data ) {

/* ... add the new record to the database */
      $this->db->insert( 'Teams', $data );

/* ... get the unique numeric ID that was associated with the record */
      $teamID = $this->db->insert_id();
      $teamID = $teamID > 0 ? $teamID : -1;

/* ... time  to go */
      return( $teamID );
    }



/*****
 * Function: updateTeam
 *
 * Arguments:
 *    $teamID - numeric ID of account to be updated
 *    $data -  associative array with data for fields for a existing user record
 *
 * Returns:
 *    -none-
 *
 *****/
   function updateTeam( $teamID, $data ) {

/* ... update the record in the database */
      $this->db->where( 'TeamID', $teamID );
      $this->db->update( 'Teams', $data );

/* ... time  to go */
      return;
    }



/*****
 * Function: checkTeam (See if we have team record for a given team name)
 *
 * Arguments:
 *    $teamName - team name to be checked
 *
 * Returns:
 *    boolean value indicating match or not for given team name
 *
 *****/
   function checkTeam( $teamName ) {

/* ... form the database query */
      $this->db->select( 'TeamID' );
      $this->db->where( 'TeamName', $teamName );
      $this->db->limit( 1 );

/* ... see if team matching name exists */
      $sqlQuery = $this->db->get( 'Teams' );
      $retFlag = $sqlQuery->num_rows() > 0 ? true : false;

/* ... time to go */
      return( $retFlag );
    }



/*****
 * Function: getTeamName (Get team name given a numeric team ID)
 *
 * Arguments:
 *    $teamID - team id to be checked
 *
 * Returns:
 *    string team name
 *
 *****/
   function getTeamName( $teamID ) {

/* ... data declaration */
      $teamName = "";

/* ... form the database query */
      $this->db->select( 'TeamName' );
      $this->db->where( 'TeamID', $teamID );
      $this->db->limit( 1 );

/* ... see if team matching name exists */
      $sqlQuery = $this->db->get( 'Teams' );
      if ($sqlQuery->num_rows() > 0) {
         $dbRow = $sqlQuery->row_array();
         $teamName = $dbRow['TeamName'];
       }

/* ... time to go */
      return( $teamName );
    }



/*****
 * Function: getTeamID (Get team ID given a team name)
 *
 * Arguments:
 *    $teamName - team name to be checked
 *
 * Returns:
 *    int team id
 *
 *****/
   function getTeamID( $teamName ) {

/* ... data declaration */
      $teamID = -1;

/* ... form the database query */
      $this->db->select( 'TeamID' );
      $this->db->where( 'TeamName', $teamName );
      $this->db->limit( 1 );

/* ... see if team matching name exists */
      $sqlQuery = $this->db->get( 'Teams' );
      if ($sqlQuery->num_rows() > 0) {
         $dbRow = $sqlQuery->row_array();
         $teamID = $dbRow['TeamID'];
       }

/* ... time to go */
      return( $teamID );
    }



/*****
 * Function: getDivision (Get division a given team with a numeric team ID plays in)
 *
 * Arguments:
 *    $teamID - team id to be checked
 *
 * Returns:
 *    string division letter
 *
 *****/
   function getDivision( $teamID ) {

/* ... data declaration */
      $teamDiv = "";

/* ... form the database query */
      $this->db->select( 'Division' );
      $this->db->where( 'TeamID', $teamID );
      $this->db->limit( 1 );

/* ... see if team matching name exists */
      $sqlQuery = $this->db->get( 'Teams' );
      if ($sqlQuery->num_rows() > 0) {
         $dbRow = $sqlQuery->row_array();
         $teamDiv = $dbRow['Division'];
       }

/* ... time to go */
      return( $teamDiv );
    }



/*****
 * Function: getListOfTeams (Get the list of registered teams)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    sparse array with teams in the index corresponding to their team ID
 *
 *****/
   function getListOfTeams() {

/* ... data declarations */
      $teamList = array();

/* ... form the database query */
      $this->db->select( 'TeamID, TeamName' );
      $this->db->order_by( 'TeamName', 'asc' );

/* ... issue the query and if data found, put it in the return array */
      $sqlQuery = $this->db->get( 'Teams' );
      if ($sqlQuery->num_rows() > 0) {
         foreach ($sqlQuery->result_array() as $dbRow) {
            $teamList[$dbRow['TeamID']] = $dbRow['TeamName'];
          }
       }

/* ... time to go */
      return( $teamList );
    }



/*****
 * Function: getListOfDivisions (Get the list of divisions in league)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    array of divisions
 *
 *****/
   function getListOfDivisions() {

/* ... data declarations */
      $divList = array();

/* ... form the database query */
      $this->db->select( 'Division' );
      $this->db->distinct();
      $this->db->order_by( 'Division', 'asc' );

/* ... issue the query and if data found, put it in the return array */
      $sqlQuery = $this->db->get( 'Teams' );
      if ($sqlQuery->num_rows() > 0) {
         foreach ($sqlQuery->result_array() as $dbRow) {
            $divList[]= $dbRow['Division'];
          }
       }

/* ... time to go */
      return( $divList );
    }



/*****
 * Function: getTeamsInDivision (Get the list teams, by numeric ID, playing in a division)
 *
 * Arguments:
 *    $division - division to get list of teams about
 *    $alphaSort - whether data is sorted by team name and not numeric ID
 *
 * Returns:
 *    array of team numeric IDs in division
 *
 *****/
   function getTeamsInDivision( $division, $alphaSort = FALSE ) {

/* ... data declarations */
      $teamList = array();

/* ... form the database query */
      $this->db->select( 'TeamID' );
      $this->db->where( 'Division', $division );
      if (!$alphaSort) {
         $this->db->order_by( 'TeamID', 'asc' );
       }
      else {
         $this->db->order_by( 'TeamName', 'asc' );
       }

/* ... issue the query and if data found, put it in the return array */
      $sqlQuery = $this->db->get( 'Teams' );
      if ($sqlQuery->num_rows() > 0) {
         foreach ($sqlQuery->result_array() as $dbRow) {
            $teamList[] = $dbRow['TeamID'];
          }
       }

/* ... time to go */
      return( $teamList );
    }



/*****
 * Function: areWeACaptain (Returns numeric teamID if provided userID maps as a contact to a team)
 *
 * Arguments:
 *    $userID - user id to be checked
 *
 * Returns:
 *    numeric team ID if contact found, otherwise -1
 *
 *****/
   function areWeACaptain( $userID ) {

/* ... data declaration */
      $teamID = -1;

/* ... form the database query */
      $this->db->select( 'TeamID' );
      $this->db->where( 'CaptainID', $userID );
      $this->db->or_where( 'CoCaptainID', $userID );
      $this->db->or_where( 'ThirdContactID', $userID );
      $this->db->limit( 1 );

/* ... see if team matching name exists */
      $sqlQuery = $this->db->get( 'Teams' );
      if ($sqlQuery->num_rows() > 0) {
         $dbRow = $sqlQuery->row_array();
         $teamID = $dbRow['TeamID'];
       }

/* ... time to go */
      return( $teamID );
    }



/*****
 * Function: buildTeamMailingList (Provides the email addresses for a team's contacts)
 *
 * Arguments:
 *    $teamID - numeric ID associated with each team (meant to be unique per team)
 *
 * Returns:
 *    array with email addresses for any team contacts
 *
 *****/
   function buildTeamMailingList( $teamID ) {

/* ... data declarations */
      $dbRow = array();
      $contactEmails = array();

/* ... form the database query */
      $this->db->select( 'CaptainID, CoCaptainID, ThirdContactID, CaptainEmail, CoCaptainEmail, ThirdContactEmail' );
      $this->db->where ('TeamID', $teamID );
      $this->db->limit( 1 );

/* ... issue the query and if data found, put it in the return array */
      $sqlQuery = $this->db->get( 'Teams' );
      if ($sqlQuery->num_rows() > 0) {
         $dbRow = $sqlQuery->row_array();

/* ... we need to determine if we use an account's email address or a stored email address for each contact */
         if ($dbRow['CaptainID'] != -1) {
            $contacts = $this->Model_Account->getAccountEmailAddr( $dbRow['CaptainID'] );
            $contactEmails = array_merge( $contactEmails, $contacts );
          }
         elseif ($dbRow['CaptainEmail'] != "") {
            $contactEmails[] = $dbRow['CaptainEmail'];
          }

         if ($dbRow['CoCaptainID'] != -1) {
            $contacts = $this->Model_Account->getAccountEmailAddr( $dbRow['CoCaptainID'] );
            $contactEmails = array_merge( $contactEmails, $contacts );
          }
         elseif ($dbRow['CoCaptainEmail'] != "") {
            $contactEmails[] = $dbRow['CoCaptainEmail'];
          }

         if ($dbRow['ThirdContactID'] != -1) {
            $contacts = $this->Model_Account->getAccountEmailAddr( $dbRow['ThirdContactID'] );
            $contactEmails = array_merge( $contactEmails, $contacts );
          }
         elseif ($dbRow['ThirdContactEmail'] != "") {
            $contactEmails[] = $dbRow['ThirdContactEmail'];
          }

       }

/* ... time to go */
      return( $contactEmails );
    }



/*****
 * Function: buildTeamContacts (Provides the details for a team's contacts)
 *
 * Arguments:
 *    $teamID - numeric ID associated with each team (meant to be unique per team)
 *
 * Returns:
 *    array with email addresses for any team contacts
 *
 *****/
   function buildTeamContacts( $teamID ) {

/* ... data declarations */
      $dbRow = array();
      $contactDetails = array();

/* ... form the database query */
      $this->db->select( 'CaptainID, CoCaptainID, ThirdContactID, CaptainEmail, CoCaptainEmail, ThirdContactEmail' );
      $this->db->where ('TeamID', $teamID );
      $this->db->limit( 1 );

/* ... issue the query and if data found, put it in the return array */
      $sqlQuery = $this->db->get( 'Teams' );
      if ($sqlQuery->num_rows() > 0) {
         $dbRow = $sqlQuery->row_array();

/* ... get the contact details for the team's Captain */
         if ($dbRow['CaptainID'] != -1) {
            $contactInfo['UserID'] = $dbRow['CaptainID'];
            $contactInfo['EmailAddr'] = $this->Model_Account->getAccountEmailAddr( $dbRow['CaptainID'] );
            $contactInfo['PhoneNr'] = $this->_getPhoneInfo( $dbRow['CaptainID'] );
            $contactInfo['Name'] = $this->Model_Account->getName( $dbRow['CaptainID'] );
          }
         else {
            $contactInfo['EmailAddr'] = array( $dbRow['CaptainEmail'] );
            $contactInfo['UserID'] = -1;
            $contactInfo['PhoneNr'] = -1;
            $contactInfo['Name'] = NULL;
          }
         $contactDetails['Captain'] = $contactInfo;

/* ... get the contact details for the team's CoCaptain */
         if ($dbRow['CoCaptainID'] != -1) {
            $contactInfo['UserID'] = $dbRow['CoCaptainID'];
            $contactInfo['EmailAddr'] = $this->Model_Account->getAccountEmailAddr( $dbRow['CoCaptainID'] );
            $contactInfo['PhoneNr'] = $this->_getPhoneInfo( $dbRow['CoCaptainID'] );
            $contactInfo['Name'] = $this->Model_Account->getName( $dbRow['CoCaptainID'] );
          }
         else {
            $contactInfo['EmailAddr'] = array( $dbRow['CoCaptainEmail'] );
            $contactInfo['UserID'] = -1;
            $contactInfo['PhoneNr'] = -1;
            $contactInfo['Name'] = NULL;
          }
         $contactDetails['CoCaptain'] = $contactInfo;

/* ... get the contact details for the team's 3rd contact */
         if ($dbRow['ThirdContactID'] != -1) {
            $contactInfo['UserID'] = $dbRow['ThirdContactID'];
            $contactInfo['EmailAddr'] = $this->Model_Account->getAccountEmailAddr( $dbRow['ThirdContactID'] );
            $contactInfo['PhoneNr'] = $this->_getPhoneInfo( $dbRow['ThirdContactID'] );
            $contactInfo['Name'] = $this->Model_Account->getName( $dbRow['ThirdContactID'] );
          }
         else {
            $contactInfo['EmailAddr'] = array( $dbRow['ThirdContactEmail'] );
            $contactInfo['UserID'] = -1;
            $contactInfo['PhoneNr'] = -1;
            $contactInfo['Name'] = NULL;
          }
         $contactDetails['ThirdContact'] = $contactInfo;

       }

/* ... time to go */
      return( $contactDetails );
    }



/*****
 * Function: _getPhoneInfo (Get the home, work and cell phone numbers for a contact)
 *
 * Arguments:
 *    $userID - numeric identifier for web account
 *
 * Returns:
 *    -none-
 *
 *****/
   function _getPhoneInfo( $userID ) {

/* ... data declarations */
      $phoneDetails = array();

/* ... get all the details on the account so we can parse out our phone information */
      $details = $this->Model_Account->getAccountDetails( $userID );

/* ... get each piece of information it existed in the record - home, work, & cell */
      if (array_key_exists( 'HomePhone', $details )) {
         $phoneDetails['Home'] = $details['HomePhone'];
       }
      else {
         $phoneDetails['Home'] = NULL;
       }

      if (array_key_exists( 'WorkPhone', $details )) {
         $phoneDetails['Work'] = $details['WorkPhone'];
       }
      else {
         $phoneDetails['Work'] = NULL;
       }


      if (array_key_exists( 'CellPhone', $details )) {
         $phoneDetails['Cell'] = $details['CellPhone'];
       }
      else {
         $phoneDetails['Cell'] = NULL;
       }

/* ... time to go */
      return ($phoneDetails );
    }



/*****
 * Function: addStandingsInfo (Add entry to temporary standings table)
 *
 * Arguments:
 *    $data -  associative array with data for fields for a new team record
 *
 * Returns:
 *    -none-
 *
 *****/
   function addStandingsInfo( $data ) {

/* ... add the new record to the database */
      $this->db->insert( 'Temp_Standings', $data );

/* ... time  to go */
      return;
    }



/*****
 * Function: getStandingsOrder (Sort list of teams, from first to last)
 *
 * Arguments:
 *    $sortingOrder - string containing valid ORDER BY fieldset
 *
 * Returns:
 *    array with numeric team IDs, from first to last
 *
 *****/
   function getStandingsOrder( $sortOrder ) {

/* ... data declarations */
      $standingOrder = array();

/* ... form the database query */
      $this->db->select( 'TeamID' );
      $this->db->order_by ( $sortOrder );

/* ... issue the query and if data found, put it in the return array */
      $sqlQuery = $this->db->get( 'Temp_Standings' );
      if ($sqlQuery->num_rows() > 0) {
         foreach ($sqlQuery->result_array() as $dbRow) {
            $standingOrder[] = $dbRow['TeamID'];
          }
       }

/* ... time  to go */
      return( $standingOrder );
    }



/*****
 * Function: contactArriving (Has a team contact registered for the website)
 *
 * Arguments:
 *    $emailAddr - email address for new account
 *    $userID - numeric account ID for new account
 *
 * Returns:
 *    -none-
 *
 *****/
   function contactArriving( $emailAddr, $userID ) {

/* ... build the query to see if the new account's email matches any team's contact list */
      $whereClause = "CaptainEmail='".$emailAddr."' OR CoCaptainEmail='".$emailAddr."' OR ThirdContactEmail='".$emailAddr."'";
      $this->db->select( 'TeamID, CaptainEmail, CoCaptainEmail, ThirdContactEmail' );
      $this->db->where( $whereClause, NULL, FALSE );
      $this->db->limit( 1 );

/* ... issue the query */
      $sqlQuery = $this->db->get( 'Teams' );
      if ($sqlQuery->num_rows() > 0) {

/* ... if we found a match, then change the contact information from just a stored email address to point to the */
/*     new account contact details */
         $dbRow = $sqlQuery->row();
         if ($dbRow->CaptainEmail == $emailAddr) {
            $data['CaptainEmail'] = NULL;
            $data['CaptainID'] = $userID;
          }
         elseif ($dbRow->CoCaptainEmail == $emailAddr) {
            $data['CoCaptainEmail'] = NULL;
            $data['CoCaptainID'] = $userID;
          }
         elseif ($dbRow->ThirdContactEmail == $emailAddr) {
            $data['ThirdContactEmail'] = NULL;
            $data['ThirdContactID'] = $userID;
          }
         $this->updateTeam( $dbRow->TeamID, $data );

       }

/* ... time to go */
      return;
    }



/*****
 * Function: sendRegistrationEmail (Upon team registering, send confirmation email)
 *
 * Arguments:
 *    $data - information from team registration
 *
 * Returns:
 *    -none-
 *
 *****/
	function sendRegistrationEmail( $data ) {

/* ... data declarations */
		$teamID = $this->getTeamID( $data['TeamName'] );
		$scheduling_options = $this->config->item( 'scheduling_options' );

/* ... get the contact email addresses so we may use them */
		$teamContacts = $this->buildTeamContacts( $teamID );

/* ... create the email to be sent */
      $toAddr = $this->buildTeamMailingList( $teamID );

      $subject = "New team registration for ".$this->config->item( 'siteName' );

      $body  = "This email has been sent by an automated process and does not require a reply.\n\n";

      $body .= "A new team has been registered and you have been listed as one of the team contacts. The team details, as registered are:\n";

      $body .= "\n";
      $body .= "Team Name: ".$data['TeamName']."\n";
      $body .= "Division: ".$data['Division']."\n";
      $body .= "\n";
      $body .= "Captain's Email: ".$teamContacts['Captain']['EmailAddr'][0]."\n";
      $body .= "CoCaptain's Email: ".$teamContacts['CoCaptain']['EmailAddr'][0]."\n";
      $body .= "Third Contact's Email: ".$teamContacts['ThirdContact']['EmailAddr'][0]."\n";
      $body .= "\n";
      $body .= "Scheduling Option: ".$scheduling_options[$data['Scheduling']]."\n";
      $body .= "\n";
      $body .= "If any information above is incorrect or changes in the future, you may change it by logging into your account on the website ";
      $body .= "and choosing 'update team contacts' from the left hand contextual menu.\n";
      $body .= "\n";

      $body .= "If you did not recently register a team, please forward this email to ".$this->config->item( 'my_replyAddress' )." so ";
      $body .= "that we may investigate.\n";

/* ... send the email out */
      $this->Model_Mail->sendTextEmail( $toAddr, array(), array(), $subject, $body );


		// time to go
		return;
	}



	/**
	 *  Checks if current web user is known as a contact on a given Team ID
	 *
	 */
	function isUserContactOnTeam( $teamID ) {

		// Data declarations
		$contactOnTeam = FALSE;

		// Get the details on the requested team
		$teamInfo = $this->GetTeamDetails( $teamID );

		// Now see if the current user is a registered contact on that team
		if ($_SESSION['UserId'] == $teamInfo['CaptainID']) {
			$contactOnTeam = TRUE;
		}
		elseif ($_SESSION['UserId'] == $teamInfo['CoCaptainID']) {
			$contactOnTeam = TRUE;
		}
		elseif ($_SESSION['UserId'] == $teamInfo['ThirdContactID']) {
			$contactOnTeam = TRUE;
		}

		// time to go
		return $contactOnTeam;
	}



 } /* ... end of Class */
