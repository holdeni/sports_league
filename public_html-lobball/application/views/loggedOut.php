<?php
// $Id: loggedOut.php 84 2011-04-13 19:22:44Z Henry $
// Last Change: $Date: 2011-04-13 15:22:44 -0400 (Wed, 13 Apr 2011) $

if (array_key_exists( "loginMsg", $_SESSION )) {
   if ($_SESSION['loginMsg'] == "invalid") {
?>
<p class="error">Incorrect email address and/or password. Please try again.</p>

<?php
    }
 }

echo form_open( "mainpage/processLogin" );

if (array_key_exists( "loginMsg", $_SESSION )) {
   if ($_SESSION['loginMsg'] != "verified") {
      echo validation_errors("<p class='error'>", "</p>");
    } 
 }
?>
Login: <br />

<?php
echo form_label( "Email Address<br />", "email" );
$data = array( 
   'name' => "email",
   'id'   => "email",
   'size' => 20,
   'value' => set_value( 'email' ),
 );
echo form_input( $data );

echo form_label( "Password<br />", "password" );
$data = array( 
   'name' => "password", 
   'id'   => "password", 
   'size' => 20,
   'value' => set_value( 'password' ),
 );
echo form_password( $data );

//echo form_label( "Remember me on computer? ", "remember" );
//$data = array( 
//   'name' => "remember",
//   'id'   => "remember",
// );
//echo form_checkbox( $data );

echo br( 2 );
echo form_submit( "login", "Login" );
echo "&nbsp;";
echo form_reset( "clear", "Reset" );

echo form_close();

echo br( 1 );
echo anchor( "mainpage/register", "register" );
echo nbs( 3 );
echo anchor( "mainpage/recover", "forget?" );

?>