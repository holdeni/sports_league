<?php
// $Id: model_mail.php 84 2011-04-13 19:22:44Z Henry $
// Last Change: $Date: 2011-04-13 15:22:44 -0400 (Wed, 13 Apr 2011) $

class Model_Mail extends CI_Model {

   private $mailConfiguration = array();
   
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
//      $this->mailConfiguration['protocol'] = 'sendmail';
      $this->mailConfiguration['protocol'] = 'mail';
      $this->mailConfiguration['charset'] = 'iso-8859-1';
      $this->mailConfiguration['wordwrap'] = TRUE;
    }



/*****
 * Function: sendTextEmail (Sends a text formatted email)
 *
 * Arguments:
 *    $toAddr - array of addresses to use as TO addressees
 *    $ccAddr - array of addresses to use as CC addressees
 *    $bccAddr - array of addresses to use as BCC addressees
 *    $subject - string for subject line
 *    $body - string comprising body of message
 *
 * Returns:
 *    -none-
 *
 *****/
   function sendTextEmail( $toAddr, $ccAddr, $bccAddr, $subject, $body ) {

/* ... set the mail configuration for TEXT */
      $this->mailConfiguration['mailtype'] = 'text';
      $this->email->initialize( $this->mailConfiguration );

/* ... set the FROM address */
      $this->email->from( $this->config->item( 'my_fromAddress' ), $this->config->item( 'my_fromName' ) );

/* ... determine if we are in a DEV environment, in which case we modify the body of the message to show */
/*     who the email would go out to normally and then modify the address set to our debug accounts */
      if ($this->config->item( "my_environment" ) == "DEV") {

         $devPreamble = "*** This is a TEST message and can safely be disregarded. ***\n\n";
         $devPreamble .= "TO: ".join( "\n", $toAddr );
         $devPreamble .= "\nCC: ".join( "\n", $ccAddr );
         $devPreamble .= "\nBCC: ".join( "\n", $bccAddr );
         $devPreamble .= "\n\n";

         $subject = "[TEST] ".$subject;
         $body = $devPreamble.$body;

         $toAddr = array( $this->config->item( 'my_devAddress' ) );
         $ccAddr = array();
         $bccAddr = array();

       }

/* ... in DEV, add our sending address as a BCC address so we can follow the email flow */
      if ($this->config->item( 'my_environment') == "DEV") {
         $bccAddr[] = $this->config->item( 'my_fromAddress' );
       }

/* ... set the TO, CC and BCC addresses, if we were provided any */
      if (count( $toAddr ) > 0) {
         $this->email->to( $toAddr );
       }
      if (count( $ccAddr ) > 0) {
         $this->email->cc( $ccAddr );
       }
      if (count( $bccAddr ) > 0) {
         $this->email->bcc( $bccAddr );
       }

/* ... set the SUBJECT and BODY of the message */
      $this->email->subject( $subject );
      $this->email->message( $body );

/* ... time to issue the email */
      $retCode = $this->email->send();

/* ... time to go */
      return;
    }



/*****
 * Function: sendHTMLEmail (Sends a text formatted email)
 *
 * Arguments:
 *    $toAddr - array of addresses to use as TO addressees
 *    $ccAddr - array of addresses to use as CC addressees
 *    $bccAddr - array of addresses to use as BCC addressees
 *    $subject - string for subject line
 *    $body - string comprising body of message
 *    $mailStyleSheet = (optional) filename of a CSS style sheet to be included in mail message
 *
 * Returns:
 *    -none-
 *
 *****/
   function sendHTMLEmail( $toAddr, $ccAddr, $bccAddr, $subject, $body, $mailStyleSheet="" ) {

/* ... set the mail configuration for TEXT */
      $this->mailConfiguration['mailtype'] = 'html';
      $this->email->initialize( $this->mailConfiguration );

/* ... set the FROM address */
      $this->email->from( $this->config->item( 'my_fromAddress' ), $this->config->item( 'my_fromName' ) );

/* ... determine if we are in a DEV environment, in which case we modify the body of the message to show */
/*     who the email would go out to normally and then modify the address set to our debug accounts */
      if ($this->config->item( "my_environment" ) == "DEV") {

         $devPreamble = "<h3>*** This is a TEST message and can safely be disregarded. ***</h3><br />";
         $devPreamble .= "<p>TO: ".join( "\n", $toAddr )."</p>";
         $devPreamble .= "<p>CC: ".join( "\n", $ccAddr )."</p>";
         $devPreamble .= "<p>BCC: ".join( "\n", $bccAddr )."</p>";
         $devPreamble .= "<br />";

         $subject = "[TEST] ".$subject;
         $body = $devPreamble.$body;

         $toAddr = array( $this->config->item( 'my_devAddress' ) );
         $ccAddr = array();
         $bccAddr = array();

       }

/* ... the body of the message needs to represent a complete web page, so add the top and bottom parts */
      $bodyPreamble  = "<html>\n";
      $bodyPreamble .= "<head>\n";
      $bodyPreamble .= "<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />\n";

/* ... if a CSS style sheet is to be incorporated, we need to read it into the appropriate HTML section */
      if ($mailStyleSheet != "") {
         $bodyPreamble .= "<style>\n";
         $cssFH = fopen( base_url()."/css/".$mailStyleSheet, "r" );
         do {
            $line = fgets( $cssFH );
            $bodyPreamble .= $line;
          }
         while (!feof( $cssFH ));
         $bodyPreamble .= "</style>\n";
       }

      $bodyPreamble .= "</head>\n";
      $bodyPreamble .= "<body>\n";
      
      $bodySuffix = "</body></html>\n";
      
      $body = $bodyPreamble.$body.$bodySuffix;

/* ... in DEV, add our sending address as a BCC address so we can follow the email flow */
      if ($this->config->item( 'my_environment') == "DEV") {
         $bccAddr[] = $this->config->item( 'my_fromAddress' );
       }

/* ... set the TO, CC and BCC addresses, if we were provided any */
      if (count( $toAddr ) > 0) {
         $this->email->to( $toAddr );
       }
      if (count( $ccAddr ) > 0) {
         $this->email->cc( $ccAddr );
       }
      if (count( $bccAddr ) > 0) {
         $this->email->bcc( $bccAddr );
       }

/* ... set the SUBJECT and BODY of the message */
      $this->email->subject( $subject );
      $this->email->message( $body );

/* ... time to issue the email */
      $this->email->send();

/* ... time to go */
      return;
    }



 } /* ... end of Model */
