<?php

// $Id: information.php 147 2011-05-26 17:52:39Z Henry $
// Last Change: $Date: 2011-05-26 13:52:39 -0400 (Thu, 26 May 2011) $

class Information extends CI_Controller {

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
   function index( $data = array()) {

/* ... define values for template variables to display on page */
      if (!array_key_exists( 'title', $data )) {
         $data['title'] = "Information - ".$this->config->item( 'siteName' );
       }

/* ... set the name of the page to be displayed */
      if (!array_key_exists( 'main', $data )) {
         $data['main'] = "information";
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
 * Function: showDocument (Display an external document within the standard template)
 *
 * Arguments:
 *    $documentName - name of HTML document stored in "data" directory
 *    $height - number of pixels to assign to frame displaying document
 *
 * Returns:
 *    -none-
 *
 *****/
   function showDocument( $documentName, $height, $subdir ) {

/* ... build the URL to the document that we desire to display */
      if (empty( $subdir )) {
         $data['externalDocument'] = base_url()."data/".$documentName;
      }
      else {
        $data['externalDocument'] = base_url()."data/".$subdir."/".$documentName;
      }
      $data['frameHeight'] = $height;

/* ... state template we will use as our standard for displaying the document */
      $data['main'] = "infoExternalDoc";
      

/* ... time to go */
      $this->index( $data );
      return;
    }



/*****
 * Function: archiveAnnouncements (Display important previous announcments)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function archiveAnnouncements() {

/* ... define values for template variables to display on page */
      $data['title'] = "Previous Announcements - ".$this->config->item( 'siteName' );

/* ... set the name of the page to be displayed */
      $data['main'] = "archiveAnnouncements";

/* ... get the set of archived announcements */
      $data['announcements'] = $this->Model_Announcement->getArchiveSet();
      $data['announcements'] = $this->Model_Announcement->reviewSet( $data['announcements'] );

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



 } /* ... end of Class */