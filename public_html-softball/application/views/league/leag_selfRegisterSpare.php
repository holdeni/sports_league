<?php
// $Id: leag_selfRegisterSpare.php 228 2012-04-10 00:53:36Z Henry $
// Last Change: $Date: 2012-04-09 20:53:36 -0400 (Mon, 09 Apr 2012) $

/* ... setup form field parameters */
/* -- sample entry 
$formFields[] = array( 
   'fieldName' => "",
   'fieldText' => "",
   'required' => true,
 );
*/

/* ... data declarations */
$formFields = array();
$data = array();
$options = array();
$buttons = array();

$formFields[] = array(
	'fieldName' => "name",
	'fieldText' => "Name",
	'required' => false,
	'type' => "input",
);
$data['name'] = array(
	'name' => 'name', 
	'id' => 'name',
	'size' => 40,
	'readonly' => 'readonly',
	'value' => htmlspecialchars( $_SESSION['FirstName']." ".$_SESSION['LastName'] ),
);

$formFields[] = array( 
   'fieldName' => "email",
   'fieldText' => "Email",
   'required' => false,
   'type' => "input",
 );
$data['email'] = array( 
   'name' => "email",
   'id'   => "email",
   'size' => 40,
   'readonly' => 'readonly',
   'value' => htmlspecialchars( $_SESSION['EmailAccount'] ),
 );

$formFields[] = array( 
   'fieldName' => "gender",
   'fieldText' => "Gender",
   'required' => true,
   'type' => "dropdown",
   'default' => $formData['Gender'],
 );
$data['gender'] = array(
	'id' => "gender",
);
$options['gender'] = array( 
   ' ' => ' ',
   'F' => 'Female',
   'M' => 'Male',
   'B' => 'Group'
 );

$formFields[] = array( 
   'fieldName' => "scheduling",
   'fieldText' => "Available Playing Options",
   'required' => true,
   'type' => "dropdown",
   'default' => $formData['Scheduling'],
 );
$data['scheduling'] = array(
   'id' => "scheduling",
 );
$options['scheduling'] = $this->config->item( "scheduling_options" );

$formFields[] = array( 
   'fieldName' => "notes",
   'fieldText' => "Additional Notes",
   'required' => false,
   'type' => "textarea",
 );
$data['notes'] = array( 
   'name' => "notes",
   'id'   => "notes",
   'rows' => 5,
   'cols' => 40,
   'value' => htmlspecialchars( $formData['Notes'] ),
 );

/* ... now for our buttons - for this form, we wish reset, cancel and submit/action */
$buttons['submit'] = array( "register", "Register As Spare Player" );
//$buttons['regular'] = array( "cancel", "Cancel" );

/* ... time to create the form */
?>

<h1>New Spare(s) Registration</h1>

<p>You may register yourself as a spare or you may act as a contact for a group of people who wish to play together. The Gender option helps
	determine if you are registering as a single spare or a group. In the Notes section you can provide additional information that will help
	teams determine if you can help them out. Your registered contact details (email and phone numbers) will be automatically visible to 
	league contacts when they view the spares list. These details will be based upon your account registration info and so if they change you
	need to change them on your account.
</p>
<p>
	 You must choose one of the 3 options around scheduling so teams can know if you will be able to meet their scheduled games.
</p>

<?php
if (array_key_exists( "registerMsg", $_SESSION )) {
   if ($_SESSION['registerMsg'] == "invalid") {
?>
<p class="error">This account has already registered as a spare. Please use another account.</p>

<?php
    	unset( $_SESSION['registerMsg'] );
    }
 }

my_DisplayForm( $formFields, $options, $data, $buttons, "league/leag_register/processRegistrationSpare");
