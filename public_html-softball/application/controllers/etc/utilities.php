<?php
// $Id: utilities.php 245 2012-05-14 23:56:23Z Henry $
// Last Change: $Date: 2012-05-14 19:56:23 -0400 (Mon, 14 May 2012) $

class Utilities extends CI_Controller {

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
/*      session_start(); */  // Enable if PHP sessions are necessary to work
    }



/*****
 * Function: nightlyDB_backup (Create ASCII export of mySQL database)
 *
 * Arguments:
 *    -none
 *
 * Returns:
 *    -none-
 *
 *****/
   function nightlyDB_backup() {

/* ... get today's date and also figure out the type of backup we are doing -- monthly, weekly or daily */
      $today = date( "Y-m-d" );
      if (date( "j" ) == 1) {
         $suffix = "-M";
         $backupType = "monthly";
       }
      else if (date( "D" ) == "Fri") {
         $suffix = "-W";
         $backupType = "weekly";
       }
      else {
         $suffix = "-D";
         $backupType = "daily";
       }

/* ... load the DB utility class */
      $this->load->dbutil();

/* ... backup your entire database and assign it to a variable */
      $backup =& $this->dbutil->backup();

/* ... load the file helper and write the file to your server */
      $this->load->helper( 'file' );
      write_file( $this->config->item( "my_backupDirectory")."/mySQL_backup-".$today.$suffix.".gz", $backup );

/* ... time to see what file rotation needs to be done for the type of backup we just performed */
      $this->_rotateBackups( $backupType );

/* ... time to go */
      return;
    }



/*****
 * Function: _rotateBackups (Rotate backup files to have appropriate number of daily, weekly and monthly)
 *
 * Arguments:
 *    $backupType - type of backups to rotate
 *
 * Returns:
 *    -none-
 *
 *****/
   function _rotateBackups( $backupType ) {

/* ... get the backup directory contents - top level only and no hidden files */
      $this->load->helper('directory');
      $dirContents = directory_map( $this->config->item( "my_backupDirectory" ), 1, FALSE );

/* ... determine which suffix we're looking for in filenames */
      switch ($backupType) {
         case "monthly":
            $suffix = "-M";
            $nrToKeep = $this->config->item( 'backups_toKeep_Monthly' );
            break;
         case "weekly":
            $suffix = "-W";
            $nrToKeep = $this->config->item( 'backups_toKeep_Weekly' );
            break;
         case "daily":
            $suffix = "-D";
            $nrToKeep = $this->config->item( 'backups_toKeep_Daily' );
            break;
       }

/* ... see which filenames contain the suffix we need */
      if (count( $dirContents ) > 0) {
         $backupFiles = array();
         foreach ($dirContents as $entry) {
            if (preg_match( "/".$suffix."/", $entry )) {
               $backupFiles[] = $entry;
             }
          }

/* ... sort the list we got (which should give us the files in reverse date order -- the ones we want to keep at top) */
         arsort( $backupFiles );

/* ... for the files we found now we keep the number we configured and delete the rest */
//         print "Number of backups found: ".count( $backupFiles )."<br />\n";
//         print "Number of backups to keep: ".$nrToKeep."<br />\n";
         if (count( $backupFiles ) > $nrToKeep  &&  $nrToKeep > 0) {
            $backupCount = 1;
//            print "   - preparing to prune un-necessary backup files<br />\n";
            foreach ($backupFiles as $entry) {
               if ($backupCount > $nrToKeep) {
                  $filename = $this->config->item( 'my_rootDirectory' ).$this->config->item( "my_backupDirectory" )."/".$entry;
                  unlink( $filename );
                }
               $backupCount++;
             }
          }

       }

/* ... time to go */
      return;
    }



/*****
 * Function: remindAboutResults (Find games without game results and remind teams to submit them)
 *
 * Arguments:
 *    $interval - how many days to allow for first reminder
 *
 * Returns:
 *    -none-
 *
 *****/
   function remindAboutResults( $interval ) {

/* ... get the list of games that we need to process reminders for */
      $gameList = $this->Model_Schedule->findMissingResults( $interval );

/* ... build the details of an email to be sent to each team involved in a game we found */
      $subjPrefix = $this->config->item( 'mail_subject_prefix')."Missing game results";
      $bodyPrefix  = "This email has been sent by an automated process and does not require a reply.\n\n";
      $bodyPrefix .= "The result of the following game has not been submitted by either team involved.\n\n";
      $bodySuffix  = "\nPlease visit the league website (".$this->config->item( 'siteURL' ).") and submit the result today. ";
      $bodySuffix .= "If you know you have submitted a result and have received this warning in error, please ";
      $bodySuffix .= "forward this email to ".$this->config->item( 'my_reply_address' )." and we will investigate.\n";

/* ... cycle through the list of missing results and send off an email for each */
      foreach ($gameList as $thisGame) {

         $toAddr = array();
         $vAddr = $this->Model_Team->buildTeamMailingList( $thisGame['VisitTeamID'] );
         $hAddr = $this->Model_Team->buildTeamMailingList( $thisGame['HomeTeamID'] );
         $toAddr = array_merge( $vAddr, $hAddr );
         $bccAddr = array( $this->config->item( 'my_fromAddress' ) );

         $subject = $subjPrefix." [".$thisGame['Date']."]";

         $body  = $bodyPrefix;
         $body .= "   Date    : ".$thisGame['Date']."\n";
         $body .= "   Time    : ".$thisGame['Time']."\n";
         $body .= "   Diamond : ".$thisGame['Diamond']."\n";
         $body .= "   Visitors: ".$this->Model_Team->getTeamName( $thisGame['VisitTeamID'] )."\n";
         $body .= "   Home    : ".$this->Model_Team->getTeamName( $thisGame['HomeTeamID'] )."\n";
         $body .= $bodySuffix;

         $this->Model_Mail->sendTextEmail( $toAddr, array(), $bccAddr, $subject, $body );
       }

/* ... time to go */
      return;
    }



/*****
 * Function: remindAboutRainouts (Find games that were rained out but not yet rescheduled)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function remindAboutRainouts() {

/* ... get the list of games that we need to remind about */
      $gameList = $this->Model_Schedule->getRainoutGames();

/* ... build the details of an email to be sent, if we have any games that aren't rescheduled */
      if (count( $gameList ) > 0) {
         $toAddr = array( $this->config->item( 'my_execAddress' ) );
         $subject = $this->config->item( 'mail_subject_prefix')."Rainout games that require rescheduling";
         $body  = "This email has been sent by an automated process and does not require a reply.\n\n";
         $body .= "The following games were reported as rainouts but as of yet have not been rescheduled.\n\n";

/* ... cycle through the list of games and then send off the email */
         foreach ($gameList as $gameID) {

            $thisGame = $this->Model_Schedule->getGameDetails( $gameID );
            $body .= "   Date    : ".$thisGame['Date']."\n";
            $body .= "   Time    : ".$thisGame['Time']."\n";
            $body .= "   Diamond : ".$thisGame['Diamond']."\n";
            $body .= "   Visitors: ".$this->Model_Team->getTeamName( $thisGame['VisitTeamID'] )."\n";
            $body .= "   Home    : ".$this->Model_Team->getTeamName( $thisGame['HomeTeamID'] )."\n";
            $body .= "---------------------------------------------\n\n";

          }
         $this->Model_Mail->sendTextEmail( $toAddr, array(), array(), $subject, $body );
       }

/* ... time to go */
      return;
    }



/*****
 * Function: remindAboutSportsmanship (Find games without sportsmanship ratings and remind teams to submit them)
 *
 * Arguments:
 *    $interval - how many days to allow for first reminder
 *
 * Returns:
 *    -none-
 *
 *****/
   function remindAboutSportsmanship( $interval ) {

/* ... get the list of games that we need to process reminders for */
      $gameList = $this->Model_Schedule->findMissingSportsmanship( $interval );

/* ... build the details of an email to be sent to each team involved in a game we found */
      $subjPrefix = $this->config->item( 'mail_subject_prefix')."Missing Sportsmanship Rating";
      $bodyPrefix  = "This email has been sent by an automated process and does not require a reply.\n\n";
      $bodyPrefix .= "Your rating of the sportsmanship of your opponents in this game has not been provided.\n\n";
      $bodySuffix  = "\nPlease visit the league website (".$this->config->item( 'siteURL' ).") and submit your rating today. ";
      $bodySuffix .= "Upon logging into the website, use 'report game result' link to provide your rating & comments. ";
      $bodySuffix .= "If you know you have submitted a result and have received this warning in error, please ";
      $bodySuffix .= "forward this email to ".$this->config->item( 'my_reply_address' )." and we will investigate.\n";

/* ... cycle through the list of missing results and send off an email for each */
      foreach ($gameList as $thisGame) {

         $bccAddr = array( "admin@otttechsoftball.org" );

         $subject = $subjPrefix." [".$thisGame['Date']."]";

         $body  = $bodyPrefix;
         $body .= "   Date    : ".$thisGame['Date']."\n";
         $body .= "   Time    : ".$thisGame['Time']."\n";
         $body .= "   Diamond : ".$thisGame['Diamond']."\n";
         $body .= "   Visitors: ".$this->Model_Team->getTeamName( $thisGame['VisitTeamID'] )."\n";
         $body .= "   Home    : ".$this->Model_Team->getTeamName( $thisGame['HomeTeamID'] )."\n";
         $body .= $bodySuffix;

         // We need to check both teams as one or both could be in trouble for not giving a sportsmanship rating
         if ($thisGame['SportsVisitRating'] == "") {
	         $toAddr = $this->Model_Team->buildTeamMailingList( $thisGame['VisitTeamID'] );
	         $this->Model_Mail->sendTextEmail( $toAddr, array(), $bccAddr, $subject, $body );
	      }
         if ($thisGame['SportsHomeRating'] == "") {
	         $toAddr = $this->Model_Team->buildTeamMailingList( $thisGame['HomeTeamID'] );
	         $this->Model_Mail->sendTextEmail( $toAddr, array(), $bccAddr, $subject, $body );
	      }

       }

/* ... time to go */
      return;
    }



 } /* ... end of Controller */
