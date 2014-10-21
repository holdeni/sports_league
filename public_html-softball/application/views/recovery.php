<?php
// $Id: recovery.php 84 2011-04-13 19:22:44Z Henry $
// Last Change: $Date: 2011-04-13 15:22:44 -0400 (Wed, 13 Apr 2011) $


echo form_open( "mainpage/processRecovery" );

if (array_key_exists( "verifMsg", $_SESSION )) {
   if ($_SESSION['verifMsg'] == "unknown") {
      echo validation_errors("<p class='error'>", "</p>");
    } 
   elseif ($_SESSION['verifMsg'] == "invalid") {
?>
<p class="error">The provided email address does not match any known accounts.</p>

<?php
    }
   elseif ($_SESSION['verifMsg'] == "verified") {
?>
<p class="info">The new password for the account has been sent to the provided email address.</p>

<?php
    }
   unset( $_SESSION['verifMsg'] );
 }
?>

<p>If you don't remember the password for your account, enter your email address in the area below
   and we'll send you a new temporary password.
</p>

<?php
echo form_label( "Email Address<br />", "email" );
$data = array( 
   'name' => "r_email",
   'id'   => "r_email",
   'size' => 40,
   'value' => set_value( 'r_email' ),
 );
echo form_input( $data );


echo br( 2 );
echo form_submit( "login", "Reset Password" );

echo form_close();
