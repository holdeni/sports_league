<?php
// $Id: model_announcement.php 147 2011-05-26 17:52:39Z Henry $
// Last Change: $Date: 2011-05-26 13:52:39 -0400 (Thu, 26 May 2011) $

class Model_Announcement extends CI_Model {

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
 * Function: getCurrentSet (Gets list of currently active announcements)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function getCurrentSet() {

/* ... data declaration */
      $announcementList = array();
      $today = date( "Y-m-d" );

/* ... build the query to find all announcements that have not yet expired */
      $this->db->select( 'AnnouncementID, ExpiryDate, Filename' );
      $this->db->where( 'ExpiryDate >=', $today );
      $this->db->order_by( 'DisplayPriority', 'asc' );

/* ... perform the query */
      $sqlQuery = $this->db->get( 'Announcements' );

/* ... now to roll through the retrieved data */
      foreach ($sqlQuery->result_array() as $dbRow) {
         $announcementList[] = $dbRow;
       }

/* ... time to go */
      return( $announcementList );
    }



/*****
 * Function: reviewSet (Verifies announcments exist and gets full pathname for easy display)
 *
 * Arguments:
 *    $noticeSet - array of announcements to be checked
 *
 * Returns:
 *    -none-
 *
 *****/
   function reviewSet( $noticeSet ) {

/* ... data declaration */
      $verifiedAnnouncements = array();

/* ... cycle through the list of announcements, building the full pathname and seeing if file actually exists */
      foreach ($noticeSet as $currAnnouncement) {
         $fullPath = $this->config->item( 'my_rootDirectory' )."announcements/".$currAnnouncement['Filename'];
         if (file_exists( $fullPath )) {
            $verifiedAnnouncements[] = array(
               'FullPath' => $fullPath,
               'AnnouncementID' => $currAnnouncement['AnnouncementID'],
//               'ExpiryDate' => $currAnnouncement['ExpiryDate'],
               'Filename' => $currAnnouncement['Filename'],
             );
          }
       }

/* ... time to go */
      return( $verifiedAnnouncements );
    }



/*****
 * Function: getArchiveSet (Gets list of archived announcements)
 *
 * Arguments:
 *    -none-
 *
 * Returns:
 *    -none-
 *
 *****/
   function getArchiveSet() {

/* ... data declaration */
      $announcementList = array();

/* ... build the query to find all announcements that have not yet expired */
      $this->db->select( 'AnnouncementID, Filename' );
      $this->db->where( 'Archive', "YES" );
      $this->db->order_by( 'DisplayPriority', 'asc' );

/* ... perform the query */
      $sqlQuery = $this->db->get( 'Announcements' );

/* ... now to roll through the retrieved data */
      foreach ($sqlQuery->result_array() as $dbRow) {
         $announcementList[] = $dbRow;
       }

/* ... time to go */
      return( $announcementList );
    }



 } /* ... end of Model */
