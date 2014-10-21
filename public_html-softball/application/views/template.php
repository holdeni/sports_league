<?php
// $Id: template.php 163 2011-06-08 13:41:10Z Henry $
// Last Change: $Date: 2011-06-08 09:41:10 -0400 (Wed, 08 Jun 2011) $

/* ... if we are in a DEV or TEST environment, add a suitable prefix to the page's title */
if ($this->config->item( 'my_environment' ) != "PROD") {
   $title = $this->config->item( 'my_environment' )." - ".$title;
 }

/* ... if we see that a directive to not cache this page is set, set an expiry date that has already passed */
if (isset( $noCache )) {
   header("Expires: Sat, 1 Jan 05:00:00 GMT");
 }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

   <head profile="http://www.w3.org/2005/10/profile">
      <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
      <meta name="robots" content="noindex,nofollow" />
      <link rel="stylesheet" type="text/css" href="<?= base_url() ?>css/style.css" />
      <link rel="icon" type="image/ico" href="<?= base_url() ?>images/favicon.ico" />
      <title><?= $title ?></title>
      <script src="<?= base_url() ?>java/jquery-1.5.1.js" type="text/javascript"></script>
      <script src="<?= base_url() ?>java/myApp-jQuery.js" type="text/javascript"></script>

<?php
   if (isset( $pageJavaScript )) {
?>
      <script  type="text/javascript">
         <?= $pageJavaScript ?>
      </script>

<?php
    }
?>

   </head>
   
   <body>
      <div id='wrapper'>
         <div id="header">
      
<?php
      $this->load->view( 'header' );
?>
      
         </div>
         
      
         <div id="nav">
      
<?php
      $this->load->view( $contextNav );
?>
      
         </div>
      
      
         <div id="main">
      
<?php
      $this->load->view( $main );
?>
      
         </div>
      
      
         <div id="footer">
      
<?php
      $this->load->view( 'footer' );
?>
      
         </div>
      
      </div>
   
   </body>

</html>