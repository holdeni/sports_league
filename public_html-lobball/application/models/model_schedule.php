<?php
// $Id: model_schedule.php 177 2011-06-28 17:12:58Z Henry $
// Last Change: $Date: 2011-06-28 13:12:58 -0400 (Tue, 28 Jun 2011) $

class Model_Schedule extends CI_Model {

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
    }



/*****
 * Function: getScheduleDetails (Get schedule information from database using some search qualifications as required)
 *
 * Arguments:
 *    
 *
 * Returns:
 *    array with schedule details
 *
 *****/
   function getScheduleDetails( $qualifier = "", $month = "" ) {

/* ... data declarations */
      $schedDetails = array();
      $onlyScheduledGames = true;
      $whereClause = "";
      
/* ... if we are doing a division schedule, we need to get the list of teams in the division */
      if ($qualifier != ""  &&  !is_numeric( $qualifier )) {
         $divTeams = $this->Model_Team->getTeamsInDivision( $qualifier );
       }

/* ... form the database query */
      $this->db->select( 'GameID, Date, Time, Diamond, HomeTeamID, HomeScore, VisitTeamID, VisitScore, Notes, Status' );
      if ($onlyScheduledGames) {
         $whereClause = "Status IN ('PLAYED', 'SCHEDULED', 'RAINOUT') ";
       }
      if ($qualifier != "") {
         if (is_numeric( $qualifier )) {
            $whereClause .= "AND (HomeTeamID=".$qualifier." OR VisitTeamID=".$qualifier.") ";
            if ($month != "") {
               $whereClause .= " AND MONTH( Date ) ='".$month."' ";
             }
          }
         else {
            $whereClause .= "AND HomeTeamID IN (".join( ",", $divTeams ).") ";
          }
       }
      $this->db->where( $whereClause );
      $this->db->order_by( 'Date', 'asc' );
      $this->db->order_by( 'Time', 'asc' );
      $this->db->order_by( 'Diamond', 'asc' );

/* ... perform the query */
      $sqlQuery = $this->db->get( 'Games' );

/* ... now to roll through the retrieved data */
      foreach ($sqlQuery->result_array() as $dbRow) {
         $schedDetails[] = $dbRow;
       }

/* ... time to go */
      return( $schedDetails );
    }



/*****
 * Function: gameResult (Determine results of a game given a numeric game ID)
 *
 * Arguments:
 *    $gameID - numeric game ID 
 *
 * Returns:
 *    array with results 
 *
 *****/
   function gameResult( $gameID ) {

/* ... form the query and then get the information */
      $this->db->select( 'HomeTeamID, HomeScore, VisitTeamID, VisitScore, Status' );
      $this->db->where( 'GameID', $gameID );
      $sqlQuery = $this->db->get( 'Games' );

/* ... if we didn't blow the query, get the data and then analyze the game result */
      if ($sqlQuery->num_rows() > 0) {
         $dbRow = $sqlQuery->row_array();
       }
      if ($dbRow['Status'] == "PLAYED") {
         if ($dbRow['HomeScore'] > $dbRow['VisitScore']) {
            $results['Result'] = "HOME";
            $results['Winner'] = $dbRow['HomeTeamID'];
            $results['Loser'] = $dbRow['VisitTeamID'];
            $results['RunDiff'] = $dbRow['HomeScore'] - $dbRow['VisitScore'];
          }
         elseif ($dbRow['HomeScore'] < $dbRow['VisitScore']) {
            $results['Result'] = "VISIT";
            $results['Winner'] = $dbRow['VisitTeamID'];
            $results['Loser'] = $dbRow['HomeTeamID'];
            $results['RunDiff'] = $dbRow['VisitScore'] - $dbRow['HomeScore'];
          }
         else {
            $results['Result'] = "TIE";
            $results['Winner'] = $dbRow['HomeTeamID'];
            $results['Loser'] = $dbRow['VisitTeamID'];
            $results['RunDiff'] = 0;
          }
       }
      else {
         $results['Result'] = "";
         $results['Winner'] = "";
         $results['Loser'] = "";
         $results['RunDiff'] = "";
       }

/* ... time to go */
      return( $results );
    }



/*****
 * Function: getGameDetails (Get schedule information from database on a specific game)
 *
 * Arguments:
 *    $gameID - numeric identifier for game
 *
 * Returns:
 *    array with game details
 *
 *****/
   function getGameDetails( $gameID ) {

/* ... data declarations */
      $schedDetails = array();
      
/* ... form the database query */
      $this->db->select( 'GameID, Date, Time, Diamond, HomeTeamID, HomeScore, VisitTeamID, VisitScore, Notes, Status' );
      $this->db->where( "GameID", $gameID );

/* ... perform the query */
      $sqlQuery = $this->db->get( 'Games' );

/* ... now to roll through the retrieved data */
      foreach ($sqlQuery->result_array() as $dbRow) {
         $schedDetails = $dbRow;
       }

/* ... time to go */
      return( $schedDetails );
    }



/*****
 * Function: getSetOfGames (Get schedule information from database on a games within a range)
 *
 * Arguments:
 *    $startDate - Y-M-D formatted start date
 *    $endDate - Y-M-D formatted end date
 *    $allGames - boolean, when true all games are retrieved, when false only scheduled/played/rainout games included
 *
 * Returns:
 *    array with game details
 *
 *****/
   function getSetOfGames( $startDate, $endDate, $allGames ) {

/* ... data declarations */
      $schedDetails = array();
      
/* ... form the database query */
      $this->db->select( 'GameID, Date, Time, Diamond, HomeTeamID, HomeScore, VisitTeamID, VisitScore, Notes, Status' );
      $this->db->where( "Date >=", $startDate );
      $this->db->where( "Date <=", $endDate );
      if (!$allGames) {
         $this->db->where_in( 'Status', array( 'SCHEDULED', 'RAINOUT', 'PLAYED' ) );
       }
      $this->db->order_by( 'Date', 'asc' );
      $this->db->order_by( 'Time', 'asc' );
      $this->db->order_by( 'Diamond', 'asc' );

/* ... perform the query */
      $sqlQuery = $this->db->get( 'Games' );

/* ... now to roll through the retrieved data */
      foreach ($sqlQuery->result_array() as $dbRow) {
         $schedDetails[] = $dbRow;
       }

/* ... time to go */
      return( $schedDetails );
    }



/*****
 * Function: saveGameResult
 *
 * Arguments:
 *    $gameID -  numeric game ID that results are to be updated for
 *    $fieldData - associative array containing field names and new values
 *
 * Returns:
 *    -none-
 *
 *****/
   function saveGameResult( $gameID, $fieldData ) {

/* ... update the appropriate record in the database */
      $this->db->where( 'GameID', $gameID );
      $this->db->update( 'Games', $fieldData );

/* ... time  to go */
      return;
    }



/*****
 * Function: getNumberOfRainouts (Get number of rained out games requiring rescheduling)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    integer number of rainout games
 *
 *****/
   function getNumberOfRainouts() {

/* ... form the query and then get the information */
      $this->db->select( 'GameID' );
      $this->db->where( 'Status', "RAINOUT" );
      $sqlQuery = $this->db->get( 'Games' );

/* ... however many rows we get back is our value we wanted*/
      $nrRainouts = $sqlQuery->num_rows();

/* ... time to go */
      return( $nrRainouts );
    }



/*****
 * Function: getRainoutGames (Get numeric ids for games listed as rainouts)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    array with game ids
 *
 *****/
   function getRainoutGames() {

/* ... data declarations */
      $rainoutList = array();
      
/* ... form the database query */
      $this->db->select( 'GameID' );
      $this->db->where( "Status", "RAINOUT" );

/* ... perform the query */
      $sqlQuery = $this->db->get( 'Games' );

/* ... now to roll through the retrieved data */
      foreach ($sqlQuery->result_array() as $dbRow) {
         $rainoutList[] = $dbRow['GameID'];
       }

/* ... time to go */
      return( $rainoutList );
    }



/*****
 * Function: getOpenSlots (Get details on diamond times without games)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    array with info on date, time and diamond slots open
 *
 *****/
   function getOpenSlots() {

/* ... data declarations */
      $openSlotList = array();
      
/* ... form the database query */
      $this->db->select( 'GameID, Date, Time, Diamond' );
      $whereClause = 'Status = "OPEN" AND Date >= NOW()';
      $this->db->where( $whereClause ); 

/* ... perform the query */
      $sqlQuery = $this->db->get( 'Games' );

/* ... now to roll through the retrieved data */
      foreach ($sqlQuery->result_array() as $dbRow) {
         $openSlotList[] = $dbRow;
       }

/* ... time to go */
      return( $openSlotList );
    }



/*****
 * Function: getRemainingGames (Get schedule information on games remaining for a team(s))
 *
 * Arguments:
 *    $teamList - array of numeric team IDs
 *
 * Returns:
 *    array with schedule details
 *
 *****/
   function getRemainingGames( $teamList ) {

/* ... data declarations */
      $schedDetails = array();
      $inClause = join( ",", $teamList );
      $whereClause = "(HomeTeamID IN (".$inClause.") OR VisitTeamID IN (".$inClause.")) AND Status='SCHEDULED' AND Date >= NOW()";
      
/* ... form the database query */
      $this->db->select( 'GameID, Date, Time, Diamond, HomeTeamID, VisitTeamID, Notes, Status' );
      $this->db->where( $whereClause);
      $this->db->order_by( 'Date', 'asc' );
      $this->db->order_by( 'Time', 'asc' );
      $this->db->order_by( 'Diamond', 'asc' );

/* ... perform the query */
      $sqlQuery = $this->db->get( 'Games' );

/* ... now to roll through the retrieved data */
      foreach ($sqlQuery->result_array() as $dbRow) {
         $schedDetails[] = $dbRow;
       }

/* ... time to go */
      return( $schedDetails );
    }



/*****
 * Function: findMissingResults (Find games that don't have results submitted)
 *
 * Arguments:
 *    $interval - number of days to forgive missing results
 *
 * Returns:
 *    -none-
 *
 *****/
   function findMissingResults( $interval ) {

/* ... data declarations */
      $gameList = array();

/* ... build the query for the data */
      $this->db->select( 'GameID, Date, Time, Diamond, HomeTeamID, VisitTeamID' );
      $where = "Date <= DATE_SUB( NOW(), INTERVAL ".$interval." DAY ) AND Status='SCHEDULED'";
      $this->db->where( $where );
      $this->db->order_by( 'Date', 'asc' );
      $this->db->order_by( 'Time', 'asc' );
      $this->db->order_by( 'Diamond', 'asc' );

/* ... run the query */
      $sqlQuery = $this->db->get( 'Games' );

/* ... now roll through the retrieved data */
      foreach ($sqlQuery->result_array() as $dbRow ) {
         $gameList[] = $dbRow;
       }
       
/* ... time to go */
      return( $gameList );
    }



/*****
 * Function: getPlayedGamesResults (Get information on games already played for a given team)
 *
 * Arguments:
 *    $teamID - numeric identifier for team
 *
 * Returns:
 *    array with game details
 *
 *****/
   function getPlayedGamesResults( $teamID ) {

/* ... data declarations */
      $schedDetails = array();
      
/* ... form the database query */
      $this->db->select( 'GameID, HomeTeamID, VisitTeamID' );
      $whereClause = "(HomeTeamID = ".$teamID." OR VisitTeamID = ".$teamID.") AND Status = 'PLAYED'";
      $this->db->where( $whereClause );

/* ... perform the query */
      $sqlQuery = $this->db->get( 'Games' );

/* ... now to roll through the retrieved data */
      foreach ($sqlQuery->result_array() as $dbRow) {
         $schedDetails[] = $dbRow;
       }

/* ... time to go */
      return( $schedDetails );
    }



/*****
 * Function: getHeadToHeadMatches (Get games played between a set of teams)
 *
 * Arguments:
 *    $teamID - array of numeric team ids to find games they played each other
 *
 * Returns:
 *    array of numeric game ids
 *
 *****/
   function getHeadToHeadMatches( $teamID ) {

/* ... data declarations */
      $gameList = array();

/* ... build the query */
      $this->db->select( 'GameID' );
      $teamList = join( ",", $teamID );
      $whereClause = "HomeTeamID IN (".$teamList.") AND VisitTeamID IN (".$teamList.") AND Status = 'PLAYED'";
      $this->db->where( $whereClause );

/* ... perform the query and pull up the results */
      $sqlQuery = $this->db->get( 'Games' );
      foreach ($sqlQuery->result_array() as $dbRow) {
         $gameList[] = $dbRow['GameID'];
       }

/* ... time to go */
      return( $gameList );
    }



 } /* ... end of Model */