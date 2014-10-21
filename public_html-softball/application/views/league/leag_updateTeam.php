<?php
// $Id: leag_updateTeam.php 84 2011-04-13 19:22:44Z Henry $
// Last Change: $Date: 2011-04-13 15:22:44 -0400 (Wed, 13 Apr 2011) $

/* ... data declarations */
$formFields = array();
$data = array();
$options = array();
$buttons = array();

/* ... figure out the default selected Team Name & Division - since we may be a 2nd time back in the form */
$teamnameDefault = " ";
if (array_key_exists( "TeamID", $teamData )) {
   $teamnameDefault = $teamData['TeamID'];
 }

$divisionDefault = " ";
if (array_key_exists( "Division", $teamData )) {
   $divisionDefault = $teamData['Division'];
 }

/* ... Field 1 - Team Name */
$formFields[] = array( 
   'fieldName' => "teamname",
   'fieldText' => "Team Name",
   'required' => false,
   'type' => "dropdown",
   'default' => $teamnameDefault,
 );
if (!$teamSelected) {
   $options['teamname'] = array( 
      ' ' => " ",
    );
   foreach ($teamList as $teamID => $teamName) {
      $options['teamname'][$teamID] = htmlspecialchars( $teamName );
    }
 }
else {
   $options['teamname'] = array( 
      $teamData['TeamID'] => htmlspecialchars( $teamData['TeamName'] ),
    );
 }
$data['teamname'] = array(
   'id' => "teamname",
 );

/* ... if we haven't selected a team yet, the following fields will not appear in the form */
if ($teamSelected) {
   
/* ... Field 2 - Division */
   $formFields[] = array( 
      'fieldName' => "division",
      'fieldText' => "Division",
      'required' => true,
      'type' => "dropdown",
      'default' => $divisionDefault,
    );
if ($this->Model_Account->hasAuthority( "COMMISH" )) {
   $options['division'] = array( 
      ' ' => " ",
      'A' => "A", 
      'B' => "B", 
      'C' => "C",
    );
 }
else {
   $options['division'] = array( 
      $divisionDefault => $divisionDefault,
    );
 }
   $data['division'] = array(
      'id' => "division",
    );
      
/* ... Field 3 - Captain Contact */
   $formFields[] = array( 
      'fieldName' => "captainid",
      'fieldText' => "Captain Contact",
      'required' => true,
      'type' => "input",
    );
   $data['captainid'] = array( 
      'name' => "captainid",
      'id'   => "captainid",
      'size' => 40,
      'value' => htmlspecialchars( $teamData['CaptainEmail'] ),
    );
   
/* ... Field 4 - Co-Captain Contact */
   $formFields[] = array( 
      'fieldName' => "cocaptainid",
      'fieldText' => "Co-Captain Contact",
      'required' => true,
      'type' => "input",
    );
   $data['cocaptainid'] = array( 
      'name' => "cocaptainid",
      'id'   => "cocaptainid",
      'size' => 40,
      'value' => htmlspecialchars( $teamData['CoCaptainEmail'] ),
    );
   
/* ... Field 5 - Third Contact */
   $formFields[] = array( 
      'fieldName' => "thirdcontactid",
      'fieldText' => "Third Contact",
      'required' => false,
      'type' => "input",
    );
   $data['thirdcontactid'] = array( 
      'name' => "thirdcontactid",
      'id'   => "thirdcontactid",
      'size' => 40,
      'value' => htmlspecialchars( $teamData['ThirdContactEmail'] ),
    );

 }

/* ... Field 6 - (Hidden) indicate we are updating team details */
$formFields[] = array( 
   'fieldName' => "formpass",
   'required' => false,
   'type' => "hidden",
 );
$data['formpass'] = array( 
   'formpass' => $teamSelected ? 2 : 1,
 );

/* ... now for our buttons - for this form, we wish reset, cancel and submit/action */
if (!$teamSelected) {
   $buttons['submit'] = array( "select", "Select team ..." );
 }
else {
   $buttons['submit'] = array( "select", "Update info" );
 }
//$buttons['regular'] = array( "cancel", "Cancel" );

/* ... time to create the form */
?>

<h1>Update Team Registration</h1>

<p>A team's name and the contacts associated with the team can be modified on this page.</p>

<?php
/* ... if we have a status message to display, then show it here */
if (array_key_exists( 'statusMsg', $_SESSION )) {
?>
<p class="success">Status: <?= $_SESSION['statusMsg'] ?></p>

<?php
   unset( $_SESSION['statusMsg'] );
 }

/* ... display the form using our data structures */
my_DisplayForm( $formFields, $options, $data, $buttons, "league/leag_mainpage/updateTeamDetails");
   
