<?php
// $Id: model_spares.php 232 2012-04-13 19:33:56Z Henry $
// Last Change: $Date: 2012-04-13 15:33:56 -0400 (Fri, 13 Apr 2012) $

class Model_Spares extends CI_Model {

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
 * Function: getSparesDetails
 *
 * Arguments:
 *    $userID - numeric ID associated with owner of spares record, a user ID
 *
 * Returns:
 *    associative array with field data from Spares table
 *
 *****/
   function getSparesDetails( $userID ) {

/* ... data declarations */
      $dbRow = array();

/* ... form the database query */
      $this->db->select( 'SpareID, UserId, Gender, Scheduling, Notes' );
      $this->db->where ('UserId', $userID );
      $this->db->limit( 1 );

/* ... issue the query and if data found, put it in the return array */
      $sqlQuery = $this->db->get( 'Spares' );
      if ($sqlQuery->num_rows() > 0) {
         $dbRow = $sqlQuery->row_array();
       }

/* ... time to go */
      return( $dbRow );
    }



/*****
 * Function: addSpare
 *
 * Arguments:
 *    $data -  associative array with data for fields for a new spares record
 *
 * Returns:
 *    numeric ID associated with new record (> 0) or -1 otherwise
 *
 *****/
   function addSpare( $data ) {

/* ... add the new record to the database */
      $this->db->insert( 'Spares', $data );

/* ... get the unique numeric ID that was associated with the record */
      $spareID = $this->db->insert_id();
      $spareID = $spareID > 0 ? $spareID : -1;

/* ... time  to go */
      return( $spareID );
    }



/*****
 * Function: updateSpares
 *
 * Arguments:
 *    $userID - numeric ID of user owning record to be updated
 *    $data -  associative array with data for fields for a existing spares record
 *
 * Returns:
 *    -none-
 *
 *****/
   function updateSpares( $userID, $data ) {

/* ... update the record in the database */
      $this->db->where( 'UserId', $userID );
      $this->db->update( 'Spares', $data );

/* ... time  to go */
      return;
    }



/*****
 * Function: checkSpares (See if we have spares record for a given user)
 *
 * Arguments:
 *    $userID - user ID to be checked
 *
 * Returns:
 *    boolean value indicating match or not for given user
 *
 *****/
   function checkSpares( $userID ) {

/* ... form the database query */
      $this->db->select( 'UserId' );
      $this->db->where( 'UserId', $userID );
      $this->db->limit( 1 );

/* ... see if team matching name exists */
      $sqlQuery = $this->db->get( 'Spares' );
      $retFlag = $sqlQuery->num_rows() > 0 ? true : false;

/* ... time to go */
      return( $retFlag );
    }



/*****
 * Function: getListOfSpares (Get the list of registered spares)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    sparse array with spares in the index corresponding to their team ID
 *
 *****/
   function getListOfSpares() {

/* ... data declarations */
      $sparesList = array();

/* ... form the database query */
      $this->db->select( 'SpareID, UserId, Gender, Notes, Scheduling' );
      // $this->db->order_by( 'TeamName', 'asc' );

/* ... issue the query and if data found, put it in the return array */
      $sqlQuery = $this->db->get( 'Spares' );
      if ($sqlQuery->num_rows() > 0) {
         foreach ($sqlQuery->result_array() as $dbRow) {
            $sparesList[] = $dbRow;
          }
       }

/* ... time to go */
      return( $sparesList );
    }



/*****
 * Function: sendRegistrationEmail (Upon spare registering, send confirmation email)
 *
 * Arguments:
 *    $data - information from spare registration
 *
 * Returns:
 *    -none-
 *
 *****/
	function sendRegistrationEmail( $data ) {

/* ... data declarations */
		$userID = $this->getTeamID( $data['TeamName'] );
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



 } /* ... end of Class */
