<?php
// $Id: MY_text_helper.php 84 2011-04-13 19:22:44Z Henry $
// Last Change: $Date: 2011-04-13 15:22:44 -0400 (Wed, 13 Apr 2011) $

/*****
 * Function: _formatPhoneNr (Take 10 digits and present it in nice human format)
 *
 * Arguments:
 *    $phoneNr - 10 digit phone number
 *
 * Returns:
 *    phone number in format (999) 999-9999 if legal format
 *
 *****/
function formatPhoneNr( $phoneNr ) {

/* ... data declaration */
   $phoneString = $phoneNr;           // if we cannot format the number, we'll just return what we were given
   
   if (strlen( $phoneNr ) >= 10) {
      
/* ... break the number into it's components */
      $areaCode = substr( $phoneNr, 0, 3 );
      $prefix = substr( $phoneNr, 3, 3 );
      $extension = substr( $phoneNr, 6 );

/* ... build the nice string with the components properly adorned */
      $phoneString = "(".$areaCode.") ".$prefix."-".$extension;
    }

/* ... time to go */
   return( $phoneString );
 }