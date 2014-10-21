<?php
// $Id: leag_selfRegisterTeam.php 227 2012-04-05 00:35:47Z Henry $
// Last Change: $Date: 2012-04-04 20:35:47 -0400 (Wed, 04 Apr 2012) $

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
   'value' => htmlspecialchars( $formData['TeamName'] ),
 );

$formFields[] = array( 
   'fieldName' => "division",
   'fieldText' => "Division",
   'required' => true,
   'type' => "dropdown",
   'default' => "A",
 );
$data['division'] = array(
   'id' => "division",
 );
$options['division'] = $this->config->item( "divisions" );

$formFields[] = array( 
   'fieldName' => "captain_email",
   'fieldText' => "Captain's email",
   'required' => false,
   'type' => "input",
 );
$data['captain_email'] = array( 
   'name' => "captain_email",
   'id'   => "captain_email",
   'size' => 40,
   'readonly' => 'readonly',
   'value' => htmlspecialchars( $_SESSION['EmailAccount'] ),
 );

$formFields[] = array( 
   'fieldName' => "cocaptain_email",
   'fieldText' => "CoCaptain's email",
   'required' => false,
   'type' => "input",
 );
$data['cocaptain_email'] = array( 
   'name' => "cocaptain_email",
   'id'   => "cocaptain_email",
   'size' => 40,
   'value' => htmlspecialchars( $formData['CoCaptainEmail'] ),
 );

$formFields[] = array( 
   'fieldName' => "thirdcontact_email",
   'fieldText' => "Third Contact's email",
   'required' => false,
   'type' => "input",
 );
$data['thirdcontact_email'] = array( 
   'name' => "thirdcontact_email",
   'id'   => "thirdcontact_email",
   'size' => 40,
   'value' => htmlspecialchars( $formData['ThirdContactEmail'] ),
 );

$formFields[] = array( 
   'fieldName' => "scheduling",
   'fieldText' => "Required Scheduling For Team",
   'required' => true,
   'type' => "dropdown",
   'default' => $formData['Scheduling'],
 );
$data['scheduling'] = array(
   'id' => "scheduling",
 );
$options['scheduling'] = $this->config->item( "scheduling_options" );

/* ... now for our buttons - for this form, we wish reset, cancel and submit/action */
$buttons['submit'] = array( "register", "Register team" );
//$buttons['regular'] = array( "cancel", "Cancel" );

/* ... time to create the form */
?>

<h1>New Team Registration</h1>

<p>Registering a team indicates your desire to play in the league according to the league rules. At the time of registration at least one team
contact is required. If you know a 2nd or 3rd contact for you team, you can provide them now or add them later on. Teams must choose one of the
3 options around scheduling. Your choice will be used both for the season schedule when generated and for rainout rescheduling.
</p>

<?php
if (array_key_exists( "registerMsg", $_SESSION )) {
   if ($_SESSION['registerMsg'] == "invalid") {
?>
<p class="error">A team already exists with this team name. Please try again.</p>

<?php
    	unset( $_SESSION['registerMsg'] );
    }
 }

my_DisplayForm( $formFields, $options, $data, $buttons, "league/leag_register/processRegistration");
