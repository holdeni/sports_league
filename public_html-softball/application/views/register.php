<?php
// $Id: register.php 84 2011-04-13 19:22:44Z Henry $
// Last Change: $Date: 2011-04-13 15:22:44 -0400 (Wed, 13 Apr 2011) $

/* ... data declarations */
$formFields = array();
$data = array();
$options = array();
$buttons = array();

$formFields[] = array( 
   'fieldName' => "r_email",
   'fieldText' => "Email Address",
   'type' => "input",
   'required' => true,
 );
$data['r_email'] = array( 
   'name' => "r_email",
   'id'   => "r_email",
   'size' => 40,
   'value' => set_value( 'r_email' ),
 );

$formFields[] = array( 
   'fieldName' => "r_password",
   'fieldText' => "Password",
   'type' => "input",
   'required' => true,
 );
$data['r_password'] = array( 
   'name' => "r_password", 
   'id'   => "r_password", 
   'size' => 20,
   'value' => set_value( 'r_password' ),
 );

$formFields[] = array( 
   'fieldName' => "passwordConf",
   'fieldText' => "Password Confirmation",
   'type' => "input",
   'required' => true,
 );
$data['passwordConf'] = array( 
   'name' => "passwordConf", 
   'id'   => "passwordConf", 
   'size' => 20,
   'value' => set_value( 'passwordConf' ),
 );

$formFields[] = array( 
   'fieldName' => "firstname",
   'fieldText' => "First Name",
   'type' => "input",
   'required' => true,
 );
$data['firstname'] = array( 
   'name' => "firstname", 
   'id'   => "firstname", 
   'size' => 40,
   'value' => set_value( 'firstname' ),
 );

$formFields[] = array( 
   'fieldName' => "lastname",
   'fieldText' => "Last Name",
   'type' => "input",
   'required' => true,
 );
$data['lastname'] = array( 
   'name' => "lastname", 
   'id'   => "lastname", 
   'size' => 40,
   'value' => set_value( 'lastname' ),
 );

$formFields[] = array( 
   'fieldName' => "homephone",
   'fieldText' => "Home Phone Number",
   'type' => "input",
   'required' => true,
 );
$data['homephone'] = array( 
   'name' => "homephone", 
   'id'   => "homephone", 
   'size' => 20,
   'value' => set_value( 'homephone' ),
 );

$formFields[] = array( 
   'fieldName' => "workphone",
   'fieldText' => "Work Phone Number",
   'type' => "input",
   'required' => false,
 );
$data['workphone'] = array( 
   'name' => "workphone", 
   'id'   => "workphone", 
   'size' => 20,
   'value' => set_value( 'workphone' ),
 );

$formFields[] = array( 
   'fieldName' => "cellphone",
   'fieldText' => "Cell Phone Number",
   'type' => "input",
   'required' => false,
 );
$data['cellphone'] = array( 
   'name' => "cellphone", 
   'id'   => "cellphone", 
   'size' => 20,
   'value' => set_value( 'cellphone' ),
 );

$formFields[] = array( 
   'fieldName' => "altemail",
   'fieldText' => "Alternative Email Address",
   'type' => "input",
   'required' => false,
 );
$data['altemail'] = array( 
   'name' => "altemail", 
   'id'   => "altemail", 
   'size' => 40,
   'value' => set_value( 'altemail' ),
 );

/* ... now for our buttons, if we haven't yet successfully save a new account */
if (!isset( $_SESSION['registerMsg'] )  ||  $_SESSION['registerMsg'] != "valid") {
   $buttons['submit'] = array( "register", "Register account" );
 }

/* ... time to create the form */
?>

<h1>New Account Creation</h1>

<p>Complete the required fields and press Register to have an account created.</p>

<?php
/* ... if we have a status message to display, then show it here */
if (array_key_exists( "registerMsg", $_SESSION )) {
   if ($_SESSION['registerMsg'] == "invalid") {
?>
<p class="error">An account is already associated with this email address. Please try again.</p>

<?php

    }
   elseif ($_SESSION['registerMsg'] == "valid") {
?>
<p class="success">Status: Account details successfully saved. You have been automatically logged in.</p>

<?php
    }
   unset( $_SESSION['registerMsg'] );
 }

/* ... display the form */
my_DisplayForm( $formFields, $options, $data, $buttons, "mainpage/processRegistration");
?>

<h3>Notes</h3>
<ol>
   <li>Phone numbers are 10 digits only (no extensions) and are entered without spaces or hyphens.<br />
      For example:<br />
      Correct: 6135551212<br />
      Incorrect: 613-555-1212 or (613) 555-1212 or 555-1212 or 5551212 <br />
   </li>
   <li>Password must be 8 characters long and consist of only alphanumeric characters (letters and digits). No special characters or
      spaces. At least one letter and one digit must included in the password. Password can be longer than 8 characters.
   </li>
   <li>Alternative email address is just used when sending emails out to your account. It is not acceptable as a login email address. 
   </li>
</ol>