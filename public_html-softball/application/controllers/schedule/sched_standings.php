<?php

// $Id: sched_standings.php 254 2012-08-01 17:17:40Z Henry $
// Last Change: $Date: 2012-08-01 13:17:40 -0400 (Wed, 01 Aug 2012) $

class Sched_standings extends CI_Controller {

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
 *    $data - (optional) array of variables for selected page to present
 *
 * Returns:
 *    -none-
 *
 *****/
   function index( $data = array() ) {

/* ... define values for template variables to display on page */
      if (!array_key_exists( 'title', $data )) {
         $data['title'] = "Standings - ".$this->config->item( 'siteName' );
       }

/* ... run the routine that will calculate the standings */
      list ($data['standings'] , $data['positions']) = $this->_standings();

/* ... set the name of the page to be displayed */
      if (!array_key_exists( 'main', $data )) {
         $data['main'] = "schedule/sched_standings";
       }

/* ... set up the jQuery scripts we need in order to nicely format this page */
      $data['pageJavaScript'] = "
         $(document).ready( function() {
            $('.teamRow:odd').addClass( 'odd' );
            $('.teamRow:even').addClass( 'even' );
          } );
         \n";

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
 * Function: _standings (Calculate standings for league)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    associative array with wins, losses, ties, runs and points scored by each team
 *    associative array with order of standing for each team in division
 *
 *****/
   function _standings() {

/* ... data declarations */
      $data = array();
      $positions = array();

/* ... we'll determine each division on it's own - so get the list of divisions */
      $divList = $this->Model_Team->getListOfDivisions();
      foreach ($divList as $curDivision) {

/* ... step 1: get the team IDs in the current division */
         $teamList = $this->Model_Team->getTeamsInDivision( $curDivision );

/* ... step 2: get the list of games for all teams in the division */
         $schedDetails = $this->Model_Schedule->getScheduleDetails( $curDivision );

/* ... step 3: initialize our data collection array to zero */
         foreach ($teamList as $teamID) {
            $data['wins'][$teamID] = 0;
            $data['losses'][$teamID] = 0;
            $data['ties'][ $teamID] = 0;
            $data['runsFor'][$teamID] = 0;
            $data['runsAga'][$teamID] = 0;
            $data['forfeits'][$teamID] = 0;
          }

/* ... step 4: go through the games and record the appropriate results for the games played */
         for ($i = 0; $i < count( $schedDetails ); $i++) {
            if ($schedDetails[$i]['Status'] == "PLAYED"  ||  $schedDetails[$i]['Status'] == "FORFEIT-HOME"  ||  $schedDetails[$i]['Status'] ==  "FORFEIT-VISIT") {
               $this->_processGameResult( $schedDetails[$i], $data );
             }
          }

/* ... step 5: determine the number of points each team has */
         foreach ($teamList as $teamID) {
            $data['points'][$teamID] = 2 * $data['wins'][$teamID] + $data['ties'][$teamID];
          }

/* ... step 6: determine which tiebreaker formula we are to use and then use it to get a standings order */
         if ($this->config->item( 'my_tiebreakerFormula' ) == "ADVANCED") {
            list( $positions[$curDivision], $data['tiebreaks'][$curDivision] ) = $this->_breakTieAdvanced( $teamList, $data );
          }
         elseif ($this->config->item( 'my_tiebreakerFormula' ) == "ADVANCED-2") {
         	list( $positions[$curDivision], $data['tiebreaks'][$curDivision] ) = $this->_breakTieAdvancedMethod2( $teamList, $data );
         }
         else {
            $positions[$curDivision] = $this->_breakTieBasic( $teamList, $data );
          }

       }

/* ... time to go */
      return( array( $data, $positions ) );
    }



/*****
 * Function: _processGameResult (Update ongoing tracking of wins, losses, ties and runs)
 *
 * Arguments:
 *    $gameDetails - information relating to a game
 *    $data - array with fields for each team tracking wins, losses, etc
 *
 * Returns:
 *    $data - array with fields for each team tracking wins, losses, etc
 *
 *****/
   function _processGameResult( $gameDetails, &$data ) {

/* ... did the home team win? */
      if ($gameDetails['HomeScore'] > $gameDetails['VisitScore']) {
         $data['wins'][$gameDetails['HomeTeamID']]++;
         if ($gameDetails['Status'] != "FORFEIT-VISIT") {
         	$data['losses'][$gameDetails['VisitTeamID']]++;
         }
         else {
         	$data['forfeits'][$gameDetails['VisitTeamID']]++;
         }
       }

/* ... or the visiting team? */
      elseif ($gameDetails['HomeScore'] < $gameDetails['VisitScore']) {
        	$data['wins'][$gameDetails['VisitTeamID']]++;
         if ($gameDetails['Status'] != "FORFEIT-HOME") {
	         $data['losses'][$gameDetails['HomeTeamID']]++;
	      }
         else {
         	$data['forfeits'][$gameDetails['HomeTeamID']]++;
         }
       }

/* ... otherwise it must have been a tie */
      else {
         $data['ties'][$gameDetails['HomeTeamID']]++;
         $data['ties'][$gameDetails['VisitTeamID']]++;
       }

/* ... record the number of runs each team scored or had scored upon them */
      $data['runsFor'][$gameDetails['HomeTeamID']] += $gameDetails['HomeScore'];
      $data['runsAga'][$gameDetails['HomeTeamID']] += $gameDetails['VisitScore'];
      $data['runsFor'][$gameDetails['VisitTeamID']] += $gameDetails['VisitScore'];
      $data['runsAga'][$gameDetails['VisitTeamID']] += $gameDetails['HomeScore'];

/* ... time to go */
      return;
    }



/*****
 * Function: _breakTieBasic (Create a standing order using basic tiebreaker logic)
 *
 * Arguments:
 *    $teamList - array of numeric Team IDs
 *    $data - set of standing data (e.g wins, losses, etc) useful in breaking a tie
 *
 * Returns:
 *    array of team IDs order from first to last
 *
 *****/
   function _breakTieBasic( $teamList, $data ) {

/* ... data declarations */
      $i = 1;
      $standingOrder = array();

/* ... ensure our temporary database table is empty */
   $this->db->empty_table( 'Temp_Standings' );

/* ... we will use database functions and actions to determine our standings order - so get data into a table */
   foreach ($teamList as $teamID) {
      $rowData = array(
         "TeamID" => $teamID,
         "GamesPlayed" => $data['wins'][$teamID] + $data['losses'][$teamID] + $data['ties'][$teamID],
         "Wins" => $data['wins'][$teamID],
         "Losses" => $data['losses'][$teamID],
         "Ties" => $data['ties'][$teamID],
         "Points" => $data['points'][$teamID],
         "RunsFor" => $data['runsFor'][$teamID],
         "RunsAga" => $data['runsAga'][$teamID],
         "RunsDelta" => $data['runsFor'][$teamID] - $data['runsAga'][$teamID],
         "Position" => $i++,
       );
      $this->Model_Team->addStandingsInfo( $rowData );
    }

/* ... get a revised standings order based upon some basic criteria */
   $sortingOrder = "Points DESC, RunsDelta DESC, Wins DESC, Losses ASC, GamesPlayed DESC";
   $standingOrder = $this->Model_Team->getStandingsOrder( $sortingOrder );

/* ... time to go */
      return( $standingOrder );
    }



/*****
 * Function: headToHead (Build details on head to head results)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function headToHead() {

/* ... define values for template variables to display on page */
      $data['title'] = "Head To Head - ".$this->config->item( 'siteName' );

/* ... run the routine that will calculate the standings */
      $data['headToHead'] = $this->_determineH2H();

/* ... set the name of the page to be displayed */
      $data['main'] = "schedule/sched_headToHead";

/* ... set up the jQuery scripts we need in order to nicely format this page */
      $data['pageJavaScript'] = "
         $(document).ready( function() {
            $('.teamRow:odd').addClass( 'odd' );
            $('.teamRow:even').addClass( 'even' );
          } );
         \n";

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
 * Function: _determineH2H (Build table showing head to head results)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function _determineH2H() {

/* ... data declarations */
      $headToHead = array();

/* ... get the list of divisions */
      $divList = $this->Model_Team->getListOfDivisions();
//      $divList = array( "C" );

/* ... build a table of head to head for each division */
      foreach ($divList as $currDiv) {

/* ... get the list of teams for the division */
         $teamList = $this->Model_Team->getTeamsInDivision( $currDiv );
         $teamNames = array();
         foreach ($teamList as $teamID) {
            $teamNames[] = $this->Model_Team->getTeamName( $teamID );
          }
         sort( $teamNames );

/* ... define table parameters */
         $cellWidth = intval( 100 / (count( $teamNames ) + 1) );
         $template = array (
            'table_open' => '<table border="border" width="95%">',
            'heading_cell_start' => '<th width="'.$cellWidth.'%">',
            'row_start'  => '<tr class="teamRow">',
            'row_alt_start' => '<tr class="teamRow">',
          );
         $this->table->set_template( $template );

/* ... caption this table set */
         $this->table->set_caption( "Division ".$currDiv );

/* ... the column headers are these team names with a leading column for the current team */
         $columns = array( 'Team' );
         foreach ($teamNames as $team) {
            $columns[] = htmlspecialchars( $team );
          }
         $this->table->set_heading( $columns );

/* ... now for each team, determine how it did against each of the other teams */
         foreach ($teamNames as $currTeam) {
            $currTeamInfo = array( htmlspecialchars( $currTeam ) );
            $headToHeadRecord = $this->_getHeadToHeadRecord( $currTeam );
            foreach ($teamNames as $team) {
               if (isset( $headToHeadRecord[$this->Model_Team->getTeamID( $team )] )) {
                  $currTeamInfo[] = $headToHeadRecord[$this->Model_Team->getTeamID( $team )];
                }
               else {
                  $currTeamInfo[] = " ";
                }
             }
            $this->table->add_row( $currTeamInfo );
          }

/* ... generate the table, after protecting the data as safe HTML */
         $headToHead[$currDiv] = $this->table->generate();
       }

/* ... time to go */
      return( $headToHead );
    }



/*****
 * Function: _getHeadToHeadRecord (Get the head to head record details for a given team)
 *
 * Arguments:
 *    $teamName - name of team for whom to collect head to head details
 *
 * Returns:
 *    -none-
 *
 *****/
   function _getHeadToHeadRecord( $teamName ) {

/* ... data declarations */
      $h2hRecord = array();

/* ... determine the team ID, so we can look up the team's schedule */
      $teamID = $this->Model_Team->getTeamID( $teamName );

/* ... get the results from the games that this team has played */
      $playedGames = $this->Model_Schedule->getPlayedGamesResults( $teamID );

/* ... time to go through the results and build up the head to head details */
      foreach ($playedGames as $currGame) {

/* ... get the results from the game and determine if it is a win, loss or tie (plus the non-zero run differential) */
         $gameResult = $this->Model_Schedule->gameResult( $currGame['GameID'] );
         if ($currGame['HomeTeamID'] == $teamID) {
            if ($gameResult['Result'] == "HOME") {
               $gameInfo = "W (+".$gameResult['RunDiff'].")";
             }
            elseif ($gameResult['Result'] == "VISIT") {
               $gameInfo = "L (-".$gameResult['RunDiff'].")";
             }
            else {
               $gameInfo = "T";
             }
          }
         if ($currGame['VisitTeamID'] == $teamID) {
            if ($gameResult['Result'] == "VISIT") {
               $gameInfo = "W (+".$gameResult['RunDiff'].")";
             }
            elseif ($gameResult['Result'] == "HOME") {
               $gameInfo = "L (-".$gameResult['RunDiff'].")";
             }
            else {
               $gameInfo = "T";
             }
          }

/* ... put that information into the appropriate cross reference with the opponent */
         if ($currGame['HomeTeamID'] == $teamID) {
            $oppTeamID = $currGame['VisitTeamID'];
          }
         else {
            $oppTeamID = $currGame['HomeTeamID'];
          }
         if (!isset( $h2hRecord[$oppTeamID] )) {
            $h2hRecord[$oppTeamID] = $gameInfo;
          }
         else {
            $h2hRecord[$oppTeamID] .= "<br /> ".$gameInfo;
          }

       }

/* ... time to go */
      return( $h2hRecord );
    }



/*****
 * Function: _breakTieAdvanced (Determine order of standing using advanced tiebreaker qualifications)
 *
 * Arguments:
 *    $teamList - array of numeric team Ids in Division
 *    $data - array of arrays of standing data (wins, losses, etc) indexed by team id
 *
 * Returns:
 *    array of team ids sorted in standing order, from first to last
 *
 *****/
   function _breakTieAdvanced( $teamList, $data ) {

/* ... data declarations */
      $standingOrder = array();

/* ... pull the information for this division out of growing arrays */
      foreach ($teamList as $teamID) {
         $divTeam['points'][$teamID]  = $data['points'][$teamID];
       }

/* ... first sort is by points */
      arsort( $divTeam['points'] );

/* ... okay, so we start moving through the standings, place by place */
      $curPlace = 0;
      do {

/* ... find out teams tied with points as the current place we are checking */
         $tiedTeams = $this->_findTiedTeams( $divTeam['points'], array_keys( $divTeam['points'] ), $curPlace );

/* ... if we have tied teams, then we need to move through the tiebreaker levels */
         $tieBreakReason = "";
         $tieBroken = false;
         if (count( $tiedTeams ) > 1) {

/* ... easiest to prepare by getting both head to head info we may use - records and run diff */
            list($h2hPoints, $h2hDiff) = $this->_getHeadToHeadDetails( $tiedTeams );

/* ... first tiebreaker we'll try to apply is for head to head record */
            $points = array_values( $h2hPoints );
            if ($points[0] != $points[1]) {
               $tiedTeams = array_keys( $h2hPoints );
               $tieBroken = true;
               $tieBreakReason = "Best head to head record";
             }
            else {

/* ... we still have teams tied so remove those teams that aren't at the top level from further tiebreaker evaluation */
               $tiedTeams = array();
               $topTeam = true;
               foreach ($h2hPoints as $teamID => $value) {
                  if (!in_array( $teamID, $standingOrder )) {
                     if ($topTeam) {
                        $tiedTeams[] = $teamID;
                        $valToMatch = $value;
                        $topTeam = false;
                      }
                     elseif ($value == $valToMatch) {
                     	$tiedTeams[] = $teamID;
                      }
                   }
                }
             }

/* ... second tiebreaker we'll apply is most wins overall */
            if (!$tieBroken) {
               foreach ($tiedTeams as $teamID) {
                  $divTeam['wins'][$teamID] = $data['wins'][$teamID];
                }
               arsort( $divTeam['wins'] );
               $wins = array_values( $divTeam['wins'] );
               if ($wins[0] != $wins[1]) {
                  $tiedTeams = array_keys( $divTeam['wins'] );
                  $tieBroken = true;
                  $tieBreakReason = "Most overall wins";
                }
               else {

/* ... we still have teams tied so remove those teams that aren't at the top level from further tiebreaker evaluation */
                  $tiedTeams = array();
                  $topTeam = true;
                  foreach ($divTeam['wins'] as $teamID => $value) {
                     if (!in_array( $teamID, $standingOrder )) {
                        if ($topTeam) {
                           $tiedTeams[] = $teamID;
                           $valToMatch = $value;
                           $topTeam = false;
                         }
                        elseif ($value == $valToMatch) {
                        	$tiedTeams[] = $teamID;
                         }
                      }
                   }
                }
             }

/* ... third tiebreaker we'll apply is head to head run differential */
            if (!$tieBroken) {

/* ... since we may have eliminated some teams in more than 3 team situations, we need to get the appropriate head to head run diff for teams left */
               list($h2hPoints, $h2hDiff) = $this->_getHeadToHeadDetails( $tiedTeams );
               $runDelta = array_values( $h2hDiff );
               if ($runDelta[0] != $runDelta[1]) {
                  $tiedTeams = array_keys( $h2hDiff );
                  $tieBroken = true;
                  $tieBreakReason = "Best head to head run differential";
                }
               else {

/* ... we still have teams tied so remove those teams that aren't at the top level from further tiebreaker evaluation */
                  $tiedTeams = array();
                  $topTeam = true;
                  foreach ($h2hDiff as $teamID => $value) {
                     if (!in_array( $teamID, $standingOrder )) {
                        if ($topTeam) {
                           $tiedTeams[] = $teamID;
                           $valToMatch = $value;
                           $topTeam = false;
                         }
                        elseif ($value == $valToMatch) {
                        	$tiedTeams[] = $teamID;
                         }
                      }
                   }
                }
             }

/* ... fourth tiebreaker we'll apply is better run differential overall */
            if (!$tieBroken) {
               foreach ($tiedTeams as $teamID) {
                  $divTeam['runsDiff'][$teamID] = $data['runsFor'][$teamID] - $data['runsAga'][$teamID];
                }
               arsort( $divTeam['runsDiff'] );
               $runDelta = array_values( $divTeam['runsDiff'] );
               if ($runDelta[0] != $runDelta[1]) {
                  $tiedTeams = array_keys( $divTeam['runsDiff'] );
                  $tieBroken = true;
                  $tieBreakReason = "Best overall run differential";
                }
             }

          }
         else {
/* ... no team with same number of points - so easy to make the following declaration */
            $tieBroken = true;
          }

/* ... did we break the tie? */
         if ($tieBroken) {

/* ... the team at the top of the list is the team we put as our team in that standing's spot */
            $standingOrder[$curPlace] = $tiedTeams[0];
            $data['tiebreaks'][$tiedTeams[0]] = $tieBreakReason;
            $divTeam['points'][$tiedTeams[0]] = 100;     // Set the points to a high value to float the team out of further tiebreaker review
            $curPlace++;
          }
         else {
/* ... we have teams tied that we couldn't order automatically, so note them each as requiring some form of manual tiebreak */
            foreach ($tiedTeams as $teamID) {
               $standingOrder[$curPlace] = $teamID;
               $data['tiebreaks'][$teamID] = "Coin toss required";
               $divTeam['points'][$teamID] = 100;
               $curPlace++;
             }
          }

         arsort( $divTeam['points'] );
       }
      while ($curPlace < count( $teamList ));

/* ... time to go */
      return( array( $standingOrder, $data['tiebreaks'] ) );
    }



/*****
 * Function: _breakTieAdvancedMethod2 (Determine order of standing using second set of advanced tiebreaker qualifications)
 *
 * Arguments:
 *    $teamList - array of numeric team Ids in Division
 *    $data - array of arrays of standing data (wins, losses, etc) indexed by team id
 *
 * Returns:
 *    array of team ids sorted in standing order, from first to last
 *
 *****/
   function _breakTieAdvancedMethod2( $teamList, $data ) {

/* ... data declarations */
      $standingOrder = array();

/* ... pull the information for this division out of growing arrays */
      foreach ($teamList as $teamID) {
         $divTeam['points'][$teamID]  = $data['points'][$teamID];
       }

/* ... first sort is by points */
      arsort( $divTeam['points'] );

/* ... okay, so we start moving through the standings, place by place */
      $curPlace = 0;
      do {

/* ... find out teams tied with points as the current place we are checking */
         $tiedTeams = $this->_findTiedTeams( $divTeam['points'], array_keys( $divTeam['points'] ), $curPlace );

/* ... if we have tied teams, then we need to move through the tiebreaker levels */
         $tieBreakReason = "";
         $tieBroken = false;
         if (count( $tiedTeams ) > 1) {

/* ... first tiebreaker is team with least forfeits */
				$divTeam['forfeits'] = array();
	         foreach ($tiedTeams as $teamID) {
	            $divTeam['forfeits'][$teamID] = $data['forfeits'][$teamID];
	          }
	         asort( $divTeam['forfeits'] );
	         $forfeits = array_values( $divTeam['forfeits'] );
	         if ($forfeits[0] != $forfeits[1]) {
	            $tiedTeams = array_keys( $divTeam['forfeits'] );
	            $tieBroken = true;
	            $tieBreakReason = "Least forfeits";
	          }
	         else {

/* ... we still have teams tied so remove those teams that aren't at the top level from further tiebreaker evaluation */
	            $tiedTeams = array();
	            $topTeam = true;
	            foreach ($divTeam['forfeits'] as $teamID => $value) {
	               if (!in_array( $teamID, $standingOrder )) {
	                  if ($topTeam) {
	                     $tiedTeams[] = $teamID;
	                     $valToMatch = $value;
	                     $topTeam = false;
	                   }
	                  elseif ($value == $valToMatch) {
	                  	$tiedTeams[] = $teamID;
	                   }
	                }
	             }
	          }

/* ... second tiebreaker we'll try to apply is for head to head record */
	         if (!$tieBroken) {

/* ... easiest to prepare by getting both head to head info we may use - records and run diff */
            	list($h2hPoints, $h2hDiff) = $this->_getHeadToHeadDetails( $tiedTeams );

	            $points = array_values( $h2hPoints );
	            if ($points[0] != $points[1]) {
	               $tiedTeams = array_keys( $h2hPoints );
	               $tieBroken = true;
	               $tieBreakReason = "Best head to head record";
	             }
	            else {

/* ... we still have teams tied so remove those teams that aren't at the top level from further tiebreaker evaluation */
	               $tiedTeams = array();
	               $topTeam = true;
	               foreach ($h2hPoints as $teamID => $value) {
	                  if (!in_array( $teamID, $standingOrder )) {
	                     if ($topTeam) {
	                        $tiedTeams[] = $teamID;
	                        $valToMatch = $value;
	                        $topTeam = false;
	                      }
	                     elseif ($value == $valToMatch) {
	                     	$tiedTeams[] = $teamID;
	                      }
	                   }
	                }
	             }
             }

/* ... third tiebreaker we'll apply is least losses overall */
            if (!$tieBroken) {
					$divTeam['losses'] = array();
               foreach ($tiedTeams as $teamID) {
                  $divTeam['losses'][$teamID] = $data['losses'][$teamID];
                }
               asort( $divTeam['losses'] );
               $losses = array_values( $divTeam['losses'] );
               if ($losses[0] != $losses[1]) {
                  $tiedTeams = array_keys( $divTeam['losses'] );
                  $tieBroken = true;
                  $tieBreakReason = "Least losses";
                }
               else {

/* ... we still have teams tied so remove those teams that aren't at the top level from further tiebreaker evaluation */
                  $tiedTeams = array();
                  $topTeam = true;
                  foreach ($divTeam['losses'] as $teamID => $value) {
                     if (!in_array( $teamID, $standingOrder )) {
                        if ($topTeam) {
                           $tiedTeams[] = $teamID;
                           $valToMatch = $value;
                           $topTeam = false;
                         }
                        elseif ($value == $valToMatch) {
                        	$tiedTeams[] = $teamID;
                         }
                      }
                   }
                }
             }

/* ... fourth tiebreaker we'll apply is most wins overall */
            if (!$tieBroken) {
            	$divTeam['wins'] = array();
               foreach ($tiedTeams as $teamID) {
                  $divTeam['wins'][$teamID] = $data['wins'][$teamID];
                }
               arsort( $divTeam['wins'] );
               $wins = array_values( $divTeam['wins'] );
               if ($wins[0] != $wins[1]) {
                  $tiedTeams = array_keys( $divTeam['wins'] );
                  $tieBroken = true;
                  $tieBreakReason = "Most wins";
                }
               else {

/* ... we still have teams tied so remove those teams that aren't at the top level from further tiebreaker evaluation */
                  $tiedTeams = array();
                  $topTeam = true;
                  foreach ($divTeam['wins'] as $teamID => $value) {
                     if (!in_array( $teamID, $standingOrder )) {
                        if ($topTeam) {
                           $tiedTeams[] = $teamID;
                           $valToMatch = $value;
                           $topTeam = false;
                         }
                        elseif ($value == $valToMatch) {
                        	$tiedTeams[] = $teamID;
                         }
                      }
                   }
                }
             }
          }
         else {
/* ... no team with same number of points - so easy to make the following declaration */
            $tieBroken = true;
          }

/* ... did we break the tie? */
         if ($tieBroken) {

/* ... the team at the top of the list is the team we put as our team in that standing's spot */
            $standingOrder[$curPlace] = $tiedTeams[0];
            $data['tiebreaks'][$tiedTeams[0]] = $tieBreakReason;
            $divTeam['points'][$tiedTeams[0]] = 100;     // Set the points to a high value to float the team out of further tiebreaker review
            $curPlace++;
          }
         else {
/* ... we have teams tied that we couldn't order automatically, so note them each as requiring some form of manual tiebreak */
            foreach ($tiedTeams as $teamID) {
               $standingOrder[$curPlace] = $teamID;
               $data['tiebreaks'][$teamID] = "Coin toss required";
               $divTeam['points'][$teamID] = 100;
               $curPlace++;
             }
          }

         arsort( $divTeam['points'] );
       }
      while ($curPlace < count( $teamList ));

/* ... time to go */
      return( array( $standingOrder, $data['tiebreaks'] ) );
    }



/*****
 * Function: _findTiedTeams (Find teams with same points as an indicated team)
 *
 * Arguments:
 *    $points - sorted (in descending point total) associative array indexed by numeric team id
 *    $teams - array of team ids, in order as sorted in $points
 *    $curPlace - index of current place being reviewed
 *
 * Returns:
 *    array of team ids with same point total as team in reviewed place
 *
 *****/
   function _findTiedTeams( $points, $teams, $curPlace ) {

/* ... data declarations */
      $tiedTeams = array();

/* ... look for teams with the same point total as our indicated place, remembering those that are equal */
      $contFlag = true;
      $index = $curPlace;
      while ($contFlag  &&  $index < count( $teams )) {

         if ($points[$teams[$index]] == $points[$teams[$curPlace]]) {
            $tiedTeams[] = $teams[$index];
            $index++;
          }
         else {
            $contFlag = false;
          }

       }

/* ... time to go */
      return( $tiedTeams );
    }



/*****
 * Function: _getHeadToHeadDetails (Get info on head to head games seeing who won/lost/tied and also run differential)
 *
 * Arguments:
 *    $tiedTeams - array of numeric team ids of teams to compare head to head
 *
 * Returns:
 *    -none-
 *
 *****/
   function _getHeadToHeadDetails( $tiedTeams ) {

/* ... data declarations */
      foreach ($tiedTeams as $teamID) {
         $h2hPoints[$teamID] = 0;
         $h2hDiff[$teamID] = 0;
       }

/* ... get the list of games these teams played against each other */
      $h2hGames = $this->Model_Schedule->getHeadToHeadMatches( $tiedTeams );

/* ... now for each game these teams played, figure out who to record as the winner (or a tie) and run diff */
      foreach ($h2hGames as $gameID) {
         $gameDetails = $this->Model_Schedule->gameResult( $gameID );
         if ($gameDetails['Result'] != "TIE") {
            $h2hPoints[$gameDetails['Winner']] += 2;
            $h2hDiff[$gameDetails['Winner']] += $gameDetails['RunDiff'];
            $h2hDiff[$gameDetails['Loser']] -= $gameDetails['RunDiff'];
          }
         else {
            $h2hPoints[$gameDetails['Winner']] += 1;
            $h2hPoints[$gameDetails['Loser']] += 1;
          }
       }

/* ... sort the data */
      arsort( $h2hPoints );
      arsort( $h2hDiff );

/* ... time to go */
      return( array( $h2hPoints, $h2hDiff ) );
    }



/*****
 * Function: seedPlayoffs (Determine seeding of teams for tournament)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function seedPlayoffs() {

/* ... if the user tries to get here without being logged in, kick'em to the curb! */
      if (!$this->Model_Account->amILoggedIn()) {
         redirect( "mainpage/notLoggedIn", "refresh" );
       }

/* ... check to ensure user has appropriate authority to run this process */
      if (!$this->Model_Account->hasAuthority( "ADMIN" )) {
         redirect( "mainpage/index", "refresh" );
       }

/* ... define values for template variables to display on page */
      $data['title'] = "Standings Administration - ".$this->config->item( 'siteName' );

/* ... set the name of the page to be displayed */
      $data['main'] = "schedule/sched_seedTournament";

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
 * Function: processConfirmation (See if seeding should or should not proceed)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function processConfirmation() {

/* ... pretty simple - if the user selected "YES", then do it; otherwise get the heck out of Dodge! */
      if ($this->input->post( 'confirm' ) == "YES") {
         $this->_updateTournamentSchedule();
       }
      else {
         redirect( "league/leag_mainpage/index", "refresh" );
       }

/* ... time to go */
      return;
    }



/*****
 * Function: determineSeeding (Using the current standings, seed teams in each division)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function determineSeeding() {

/* ... figure out the current standings */
      list( $standings , $positions ) = $this->_standings();

/* ... for each division, store the seeding for the teams within the division in the database */
      $this->Model_Tournament->purgeSeedings();

      foreach ($positions as $curDivision => $divOrder) {
         $this->Model_Tournament->storeSeedings( $curDivision, $divOrder );
       }

/* ... time to go */
      return;
    }



/*****
 * Function: _updateTournamentSchedule (Update tournament schedule using seeding information)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function _updateTournamentSchedule() {

/* ... get the full playoff schedule */
      $tournDetails = $this->Model_Tournament->getAllDetails();

/* ... find games that have divisional seed details and update the home/visit fields with appropriate team ids */
      for ($i=0; $i < count( $tournDetails ); $i++) {

         if ($tournDetails[$i]['Status'] == "SCHEDULED") {

            $dbData = array();
            $updateRecord = FALSE;

            $seedDiv = substr( $tournDetails[$i]['TournamentID'], 0, 1 );

            if ($tournDetails[$i]['HomeTeamID'] == NULL  &&  $tournDetails[$i]['HomeSeed'] != NULL) {
               $teamID = $this->Model_Tournament->getSeedTeamID( $tournDetails[$i]['HomeSeed'], $seedDiv );
               $dbData = array_merge( $dbData, array(
                  'HomeTeamID' => $teamID,
                ) );
               $updateRecord = TRUE;
             }
            if ($tournDetails[$i]['VisitTeamID'] == NULL  &&  $tournDetails[$i]['VisitSeed'] != NULL) {
               $teamID = $this->Model_Tournament->getSeedTeamID( $tournDetails[$i]['VisitSeed'], $seedDiv );
               $dbData = array_merge( $dbData, array(
                  'VisitTeamID' => $teamID,
                ) );
               $updateRecord = TRUE;
             }

/* ... if we have to write back to the database, then let us do that */
            if ($updateRecord) {
               $this->Model_Tournament->updateGameDetails( $dbData, $tournDetails[$i]['TournamentID'] );
             }
          }
       }


/* ... time to go */
      redirect( "league/leag_mainpage", "redirect" );
      return;
    }



 } /* ... end of Class */
