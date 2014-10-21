<?php

// $Id: admin_website.php 163 2011-06-08 13:41:10Z Henry $
// Last Change: $Date: 2011-06-08 09:41:10 -0400 (Wed, 08 Jun 2011) $

class Admin_WebSite extends CI_Controller {

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
      session_start();  // Enable if PHP sessions are necessary to work
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

/* ... we can only proceed if the user is properly logged in and has appropriate role level */
      if (!$this->Model_Account->amILoggedIn()) {
         redirect( "mainpage/notLoggedIn", "refresh" );
       }
      if (!$this->Model_Account->hasAuthority( "ADMIN" )) {
         redirect( "mainpage/index", "refresh" );
       }

/* ... define values for template variables to display on page */
      if (!array_key_exists( "title", $data )) {
         $data['title'] = "Site Admin - ".$this->config->item( 'siteName' );
       }

/* ... set the name of the page to be displayed */
      if (!array_key_exists( "main", $data )) {
         $data['main'] = "webAdmin/admin_home";
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
 * Function: showBackups (Display file contents of backup directory)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function showBackups() {

/* ... data declarations */
      $data['entries'] = array();
      $dailies = 0;
      $monthlies = 0;
      $weeklies = 0;
      
/* ... we can only proceed if the user is properly logged in and has appropriate role level */
      if (!$this->Model_Account->amILoggedIn()) {
         redirect( "mainpage/notLoggedIn", "refresh" );
       }
      if (!$this->Model_Account->hasAuthority( "ADMIN" )) {
         redirect( "mainpage/index", "refresh" );
       }

/* ... get the listing for the contents of the backup directory */
      $iterator = new DirectoryIterator( $this->config->item( 'my_rootDirectory' ).$this->config->item( 'my_backupDirectory' ) ) ;

/* ... parse out the information we wish to display on our page */
      foreach ($iterator as $dirEntry) {
         if (!$dirEntry->isDot()) {
            $data['entries'][] = $dirEntry->getFilename();
            $data['details'][$dirEntry->getFilename()] = array(
               'type' => $dirEntry->getType(),
               'size' => $dirEntry->isFile() ? $dirEntry->getSize() : -1,
               'mtime' => date( "D, d M Y xxx h:i A", $dirEntry->getMTime() ),     // The 3 'xxx' will be replaced by spaces by the view process
             );

/* ... for our summary information, keep track of what type of backup this file may represent */
            if ($dirEntry->isFile()) {
               if (preg_match( "/-D/", $dirEntry->getFilename() )) {
                  $dailies++;
                }
               elseif (preg_match( "/-W/", $dirEntry->getFilename() )) {
                  $weeklies++;
                }
               elseif (preg_match( "/-M/", $dirEntry->getFilename() )) {
                  $monthlies++;
                }
             }
          }
       }

/* ... prepare the summary information */
      $data['summary']  = "<ul>\n";
      $data['summary'] .= "<li>Number of Daily backups: ".$dailies."</li>";
      $data['summary'] .= "<li>Number of Weekly backups: ".$weeklies."</li>";
      $data['summary'] .= "<li>Number of Monthly backups: ".$monthlies."</li>";
      $data['summary'] .= "</ul>\n";

/* ... prepare the basic page details */
      $data['title'] = "Backups Directory - ".$this->config->item( 'siteName' );
      $data['main'] = "webAdmin/admin_showDir";
      $data['displayFileLink'] = FALSE;

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
 * Function: showLogs (Display file contents of logs directory)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function showLogs() {

/* ... data declarations */
      $data['entries'] = array();
      $dailies = 0;
      $monthlies = 0;
      $weeklies = 0;
      
/* ... we can only proceed if the user is properly logged in and has appropriate role level */
      if (!$this->Model_Account->amILoggedIn()) {
         redirect( "mainpage/notLoggedIn", "refresh" );
       }
      if (!$this->Model_Account->hasAuthority( "ADMIN" )) {
         redirect( "mainpage/index", "refresh" );
       }

/* ... get the listing for the contents of the backup directory */
      $iterator = new DirectoryIterator( $this->config->item( 'my_rootDirectory' )."application/logs" ) ;

/* ... parse out the information we wish to display on our page */
      foreach ($iterator as $dirEntry) {
         if (!$dirEntry->isDot()) {
            $data['entries'][] = $dirEntry->getFilename();
            $data['details'][$dirEntry->getFilename()] = array(
               'type' => $dirEntry->getType(),
               'size' => $dirEntry->isFile() ? $dirEntry->getSize() : -1,
               'mtime' => date( "D, d M Y xxx h:i A", $dirEntry->getMTime() ),     // The 3 'xxx' will be replaced by spaces by the view process
             );
          }
       }

/* ... prepare the basic page details */
      $data['title'] = "Logs Directory - ".$this->config->item( 'siteName' );
      $data['main'] = "webAdmin/admin_showDir";
      $data['displayFileLink'] = TRUE;
      $data['directoryPath'] = "application/logs";

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
 * Function: showFile (Display the contents of a text file)
 *
 * Arguments:
 *    $filename - filename to display
 *    $directoryPath - relative path, from website root, containing file
 *
 * Returns:
 *    -none-
 *
 *****/
   function showFile( $filename, $directoryPath ) {

/* ... load appropriate helper module(s) */
      $this->load->helper( 'file' );
      
/* ... we can only proceed if the user is properly logged in and has appropriate role level */
      if (!$this->Model_Account->amILoggedIn()) {
         redirect( "mainpage/notLoggedIn", "refresh" );
       }
      if (!$this->Model_Account->hasAuthority( "ADMIN" )) {
         redirect( "mainpage/index", "refresh" );
       }

/* ... suck in the file to display */
      $directoryPath = urldecode( $directoryPath );
      $data['pathname'] = $directoryPath."/".$filename;
      $data['fileContents'] = read_file( $data['pathname'] );
      $data['title'] = "File Contents - ".$this->config->item( 'siteName' );
      $data['main'] = 'webAdmin/admin_showFile';

/* ... enable our template variables and then display the template, as we want it shown */      
      $this->load->vars( $data );
      $this->load->view( "fullPage" );
      
/* ... time to go */
      return;
    }



 } /* ... end of Controller */
