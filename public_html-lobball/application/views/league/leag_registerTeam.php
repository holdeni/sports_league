<?php
// $Id: leag_registerTeam.php 84 2011-04-13 19:22:44Z Henry $
// Last Change: $Date: 2011-04-13 15:22:44 -0400 (Wed, 13 Apr 2011) $

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
   'fieldName' => "teamname",
   'fieldText' => "Team Name",
   'required' => true,
   'type' => "input",
 );
$data['teamname'] = array( 
   'name' => "teamname",
   'id'   => "teamname",
   'size' => 40,
   'value' => set_value( 'teamname' ),
 );

$formFields[] = array( 
   'fieldName' => "division",
   'fieldText' => "Division",
   'required' => true,
   'type' => "dropdown",
   'default' => " ",
 );
$data['division'] = array(
   'id' => "division",
 );
$options['division'] = array( 
   ' ' => " ",
   'A' => "A", 
   'B' => "B", 
   'C' => "C",
 );

/* ... now for our buttons - for this form, we wish reset, cancel and submit/action */
$buttons['submit'] = array( "register", "Register team" );
//$buttons['regular'] = array( "cancel", "Cancel" );

/* ... time to create the form */
?>

<h1>New Team Registration</h1>

<p>Registering a team associates them with the league structures and also defines the 2 to 3 team contacts for the team.</p>

<?php
if (array_key_exists( "registerMsg", $_SESSION )) {
   if ($_SESSION['registerMsg'] == "invalid") {
?>
<p class="error">A team already exists with this team name. Please try again.</p>

<?php
    }
 }

my_DisplayForm( $formFields, $options, $data, $buttons, "league/leag_mainpage/processRegistration");
