<?php
// $Id: information.php 147 2011-05-26 17:52:39Z Henry $
// Last Change: $Date: 2011-05-26 13:52:39 -0400 (Thu, 26 May 2011) $

include "siteSpecific/information-".$this->config->item( 'siteType' ).".php";

function _getLastUpdated( $filename ) {

/* ... get last modification date for bulk file */
   $lastModified = stat( $filename );
   $fileModified = date( "d M Y", $lastModified[9] );

/* ... time to go */
   return( $fileModified );
 }
