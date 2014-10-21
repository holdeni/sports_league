<?php
// $Id: model_tournament.php 202 2011-08-23 17:02:14Z Henry $
// Last Change: $Date: 2011-08-23 13:02:14 -0400 (Tue, 23 Aug 2011) $

class Model_Tournament extends CI_Model {

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
 * Function: getAllDetails (Get complete details on all tournament games)
 *
 * Arguments:
 *    
 *
 * Returns:
 *    array with schedule details
 *
 *****/
   function getAllDetails() {

/* ... data declarations */
      $tournDetails = array();

/* ... form the database query */
      $this->db->select( 'GameID, Date, Time, Diamond, HomeSeed, VisitSeed, HomeTeamID, HomeScore, VisitTeamID, VisitScore, Notes, Status, TournamentID, WinnerNextGame, LoserNextGame' );
      $whereClause = "Status IN ('PLAYED', 'SCHEDULED', 'RAINOUT') ";
      $this->db->where( $whereClause );
      $this->db->order_by( 'Date', 'asc' );
      $this->db->order_by( 'Time', 'asc' );
      $this->db->order_by( 'Diamond', 'asc' );

/* ... perform the query */
      $sqlQuery = $this->db->get( 'Tournament' );

/* ... now to roll through the retrieved data */
      foreach ($sqlQuery->result_array() as $dbRow) {
         $tournDetails[] = $dbRow;
       }

/* ... time to go */
      return( $tournDetails );
    }



/*****
 * Function: getDivDetails (Get complete details on a division's tournament games)
 *
 * Arguments:
 *    $division - identifier of which division to get details about
 *
 * Returns:
 *    array with schedule details
 *
 *****/
   function getDivDetails( $division ) {

/* ... data declarations */
      $tournDetails = array();

/* ... form the database query */
      $this->db->select( 'GameID, Date, Time, Diamond, HomeSeed, VisitSeed, HomeTeamID, HomeScore, VisitTeamID, VisitScore, Notes, Status, TournamentID, WinnerNextGame, LoserNextGame' );
      $whereClause = "Status IN ('PLAYED', 'SCHEDULED', 'RAINOUT') AND TournamentID LIKE '".$division."%'";
      $this->db->where( $whereClause );
      $this->db->order_by( 'Date', 'asc' );
      $this->db->order_by( 'Time', 'asc' );
      $this->db->order_by( 'Diamond', 'asc' );

/* ... perform the query */
      $sqlQuery = $this->db->get( 'Tournament' );

/* ... now to roll through the retrieved data */
      foreach ($sqlQuery->result_array() as $dbRow) {
         $tournDetails[] = $dbRow;
       }

/* ... time to go */
      return( $tournDetails );
    }



/*****
 * Function: storeSeedings (Store seedings of teams in a division)
 *
 * Arguments:
 *    $division - identifier of which division to get details about
 *
 * Returns:
 *    -none-
 *
 *****/
   function storeSeedings( $division, $divOrder ) {

/* ... data declaration */
      $seed = 1;
      
/* ... store the seed information a team at a time */
      foreach ($divOrder as $teamID) {
         $dbData = array(
            'Division' => $division,
            'Seed' => $seed++,
            'TeamID' => $teamID,
          );
         $this->db->insert( "TournamentSeeds", $dbData );
       }
 
/* ... time to go */
      return;
    }



/*****
 * Function: purgeSeedings (Empty the seedings table)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function purgeSeedings() {

      $this->db->empty_table( "TournamentSeeds" );
 
/* ... time to go */
      return;
    }



/*****
 * Function: getSeedTeamID (Get the team id corresponding to a given seed for a division)
 *
 * Arguments:
 *    $seed - positional seeding being sought
 *    $division - division to look up
 *
 * Returns:
 *    numeric team id
 *
 *****/
   function getSeedTeamID( $seed, $division ) {

/* ... data declarations */
      $teamID = -99;

/* ... build the query */
      $this->db->select( "TeamID" );
      $this->db->where( "Seed", $seed );
      $this->db->where( "Division", $division );

/* ... execute the query and pull out the data */
      $sqlQuery = $this->db->get( 'TournamentSeeds' );
      if ($sqlQuery->num_rows() > 0) {
         $dbRow = $sqlQuery->row();
         $teamID = $dbRow->TeamID;
       }
      
/* ... time to go */
      return( $teamID );
    }



/*****
 * Function: getTeamSchedule (Get tournament schedule for specific team)
 *
 * Arguments:
 *    $teamID - numeric team id identifying team's schedule to get
 *
 * Returns:
 *    -none-
 *
 *****/
   function getTeamSchedule( $teamID ) {

/* ... data declarations */
      $schedDetails = array();

/* ... build the query */
      $this->db->select('GameID, Date, Time, Diamond, HomeTeamID, HomeScore, VisitTeamID, VisitScore, Notes, Status, TournamentID, WinnerNextGame, LoserNextGame' );
      $whereClause = "Status IN ('PLAYED', 'SCHEDULED', 'RAINOUT') AND (HomeTeamID=".$teamID." OR VisitTeamID=".$teamID.")";
      $this->db->where( $whereClause );
      $this->db->order_by( 'Date', 'asc' );
      $this->db->order_by( 'Time', 'asc' );
      $this->db->order_by( 'Diamond', 'asc' );

/* ... execute the query and process the results */
      $sqlQuery = $this->db->get( "Tournament" );
      foreach ($sqlQuery->result_array() as $dbRow) {
         $schedDetails[] = $dbRow;
       }

/* ... time to go */
      return( $schedDetails );
    }



/*****
 * Function: updateGameDetails (Write updated game information back to the database)
 *
 * Arguments:
 *    $dbData - associative array with appropriate information to be written
 *
 * Returns:
 *    -none-
 *
 *****/
   function updateGameDetails( $dbData, $tournamentID ) {

/* ... write the update back to the DB */
      $this->db->where( 'TournamentID', $tournamentID );
      $this->db->update( 'Tournament', $dbData );

/* ... time to go */
      return;
    }



/*****
 * Function: getAllDetails (Get complete details on all tournament games)
 *
 * Arguments:
 *    $gameID - numeric game ID to be retrieved
 *
 * Returns:
 *    array with schedule details for specific game
 *
 *****/
   function getGameDetails( $gameID ) {

/* ... data declarations */
      $tournDetails = array();

/* ... form the database query */
      $this->db->select( 'GameID, Date, Time, Diamond, HomeSeed, VisitSeed, HomeTeamID, HomeScore, VisitTeamID, VisitScore, Notes, Status, TournamentID, WinnerNextGame, LoserNextGame' );
      $this->db->where( 'GameID', $gameID );

/* ... perform the query */
      $sqlQuery = $this->db->get( 'Tournament' );

/* ... now to roll through the retrieved data */
      foreach ($sqlQuery->result_array() as $dbRow) {
         $tournDetails = $dbRow;
       }

/* ... time to go */
      return( $tournDetails );
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
      $this->db->update( 'Tournament', $fieldData );

/* ... time  to go */
      return;
    }



/*****
 * Function: determineNextGame (Update schedule for next game of winner and losing team)
 *
 * Arguments:
 *    $gameID - numeric ID for game just played
 *    $teamID -  numeric ID for team to update schedule for
 *    $winnerOrLoser - either "WINNNER" or "LOSER" to determine which game flow to follow
 *
 * Returns:
 *    numeric game ID for winner and losing teams respectively or -1 if no future game
 *
 *****/
   function determineNextGame( $gameID, $teamID, $winnerOrLoser ) {

/* ... get the details for the game just played */
      $gameDetails = $this->getGameDetails( $gameID );
      
/* ... get the details for the next game using the proper flow */
      if ($winnerOrLoser == "WINNER") {
         $nextGameDetails = $this->findGameByTournamentID( $gameDetails['WinnerNextGame'] );
       }
      else {
         if ($gameDetails['LoserNextGame'] != "") {
            $nextGameDetails = $this->findGameByTournamentID( $gameDetails['LoserNextGame'] );
          }
         else {
            return( -1 );
          }
       }
       
/* ... if we have a game to update, then figure out what to change and do it */
      if (count( $nextGameDetails ) > 0) {
         $dbData = array();
         $division = substr( $gameDetails['TournamentID'], 0, 1 );
 
/* ... if the Home Team hasn't been set, then put the winner in as the home team for now */ 
         if ($nextGameDetails['HomeTeamID'] == NULL) {
            $dbData = array(
               "GameID" => $nextGameDetails['GameID'],
               "HomeTeamID" => $teamID
             );
          }
         else {

/* ... determine who should be the home team from the 2 teams now known for the game */
            if ($this->_whoIsBetterSeed( $teamID, $nextGameDetails['HomeTeamID'], $division ) == $teamID) {
               $dbData = array( 
                  "GameID" => $nextGameDetails['GameID'],
                  "VisitTeamID" => $nextGameDetails['HomeTeamID'],
                  "HomeTeamID" => $teamID
                );
             }
            else {
               $dbData = array( 
                  "GameID" => $nextGameDetails['GameID'],
                  "VisitTeamID" => $teamID
                );
             }
          }

/* ... time to update the database */
         $this->saveGameResult( $nextGameDetails['GameID'], $dbData );

       }

/* ... time  to go */
      return( $dbData['GameID'] );
    }



/*****
 * Function: findGameByTournamentID (Get numeric game ID that corresponds to a particular tournament ID)
 *
 * Arguments:
 *    $tournamentID = tournament game identifier (e.g. A1 or C12)
 *
 * Returns:
 *    numeric game ID
 *
 *****/
   function findGameByTournamentID( $tournamentID ) {

/* ... data declarations */
      $tournGameDetails = array();

/* ... form the database query */
      $this->db->select( 'GameID, Date, Time, Diamond, HomeSeed, VisitSeed, HomeTeamID, HomeScore, VisitTeamID, VisitScore, Notes, Status, TournamentID, WinnerNextGame, LoserNextGame' );
      $this->db->where( 'TournamentID', $tournamentID );

/* ... perform the query */
      $sqlQuery = $this->db->get( 'Tournament' );

/* ... now to roll through the retrieved data */
      foreach ($sqlQuery->result_array() as $dbRow) {
         $tournGameDetails = $dbRow;
       }

/* ... time to go */
      return( $tournGameDetails );
    }



/*****
 * Function: _whoIsBetterSeed (Provide team ID of better seeded team given 2 team IDs)
 *
 * Arguments:
 *    $team1ID = numeric team identifier
 *    $team2ID = second numeric team identifier
 *
 * Returns:
 *    numeric team ID who is better seed
 *
 *****/
   function _whoIsBetterSeed( $team1ID, $team2ID ) {

/* ... data declarations */
      $bestSeeded = -1;

/* ... form the database query */
      $this->db->select( 'Seed' );
      $this->db->where( 'TeamID', $team1ID );

/* ... perform the query */
      $sqlQuery = $this->db->get( 'TournamentSeeds' );

/* ... now to roll through the retrieved data */
      foreach ($sqlQuery->result_array() as $dbRow) {
         $team1Seed = $dbRow['Seed'];
       }

/* ... form the database query */
      $this->db->select( 'Seed' );
      $this->db->where( 'TeamID', $team2ID );

/* ... perform the query */
      $sqlQuery = $this->db->get( 'TournamentSeeds' );

/* ... now to roll through the retrieved data */
      foreach ($sqlQuery->result_array() as $dbRow) {
         $team2Seed = $dbRow['Seed'];
       }

/* ... figure out who is better seeded */
      $bestSeeded = $team1Seed < $team2Seed ? $team1ID : $team2ID;
      
/* ... time to go */
      return( $bestSeeded );
    }



 } /* ... end of Model */
 